<?php

    /**
     * Checking the DNS records of a domain
     * @param $toproof - Domain
     */

    require_once 'src/dnsexplain.php';
    require_once 'src/helper.php';

    function dns_check($toproof, $tld) {

        // Strict validation (defense in depth — also done in result.php)
        if (!is_safe_domain($toproof)) {
            echo '<div class="alert alert-warning"><strong>Note!</strong> Invalid domain.</div>';
            return;
        }

        // Get authorative nameservers
        $authorativelist = get_authorative_nameservers($toproof, $tld);

        // Import the nameserverlist.yaml as array
        $yamlPath = __DIR__ . "/../nameservers.yaml";
        $custom_ns_format = [];
        $customns = parse_yaml_file_safe($yamlPath);
        if (is_array($customns)) {
            foreach ($customns as $name => $ip) {
                // Validate every custom NS entry from the yaml file
                // even though it's admin-provided: belt-and-braces.
                if (!is_string($name) || !is_string($ip)) continue;
                if (!is_safe_ip($ip) && !is_safe_domain($ip)) continue;
                if (!preg_match('/^[a-zA-Z0-9._\-]{1,63}$/', $name)) continue;
                $custom_ns_format[] = [
                    "ns_name" => $name,
                    "ns_ip"   => $ip,
                    "type"    => "custom"
                ];
            }
        }

        // Merge authorative nameservers and custom nameservers to one array
        $nameservers = array_merge($authorativelist, $custom_ns_format);

        if (empty($nameservers)) {
            echo '<div class="alert alert-warning"><strong>Note!</strong> No nameservers available for query.</div>';
            return;
        }

        // Get default dns
        $dns_array = get_default_dns($toproof, $nameservers);

        //check for divergences between nameservers
        check_dns_divergences($dns_array);

        // Print DNS
        print_dns_check($dns_array);
    }

    // -------------------------------------
    // Get DNS Records
    // -------------------------------------
    //
    // SECURITY NOTE
    //  - Every call to dig validates both the queried name AND the
    //    nameserver target with is_safe_host(). escapeshellarg alone
    //    is NOT enough, because tools like dig accept option-style
    //    arguments (e.g. "-f", "-k") that begin with a dash. By
    //    whitelisting characters first we make argument injection
    //    impossible.
    //  - We additionally separate the dig invocation from the sort
    //    pipe by running sort in PHP rather than via shell.
    // -------------------------------------

    /**
     * Run "dig +short <TYPE> <name> @<ns>" with strict validation.
     * Returns array of trimmed, non-empty output lines (sorted), or [].
     */
    function _dig_lookup($type, $name, $ns_ip) {
        // Restrict record type to a known set
        $allowed_types = ['A','AAAA','TXT','CNAME','MX','NS','PTR','SOA','SRV'];
        if (!in_array($type, $allowed_types, true)) {
            return [];
        }
        if (!is_safe_host($name)) return [];
        if (!is_safe_host($ns_ip)) return [];

        $command = "dig +short " . escapeshellarg($type)
                 . " "          . safe_shell_arg($name)
                 . " @"         . safe_shell_arg($ns_ip);
        $output = shell_exec($command);
        if ($output === null) {
            return [];
        }
        $lines = [];
        foreach (explode("\n", $output) as $line) {
            $line = trim($line);
            if ($line !== '') {
                $lines[] = $line;
            }
        }
        sort($lines);
        return $lines;
    }

    // get A records
    function get_a_records($toproof, $ns_ip) {
        return _dig_lookup('A', $toproof, $ns_ip);
    }

    // get AAAA records
    function get_aaaa_records($toproof, $ns_ip) {
        return _dig_lookup('AAAA', $toproof, $ns_ip);
    }

    // get TXT records
    function get_txt_records($toproof, $ns_ip) {
        return _dig_lookup('TXT', $toproof, $ns_ip);
    }

    // get CNAME records
    function get_cname_records($toproof, $ns_ip) {
        return _dig_lookup('CNAME', $toproof, $ns_ip);
    }

    // get mx records
    function get_mx_records($toproof, $ns_ip) {
        $raw = _dig_lookup('MX', $toproof, $ns_ip);
        $mx_addresses = [];
        foreach ($raw as $mx_entry) {
            $parts = explode(' ', $mx_entry);
            if (count($parts) < 2) continue;
            $priority = (int)$parts[0];
            $destination = remove_dot_if_last_char($parts[1]);
            $mx_addresses[] = ['priority' => $priority, 'destination' => $destination];
        }
        // Sort by priority for determinism
        usort($mx_addresses, function($a, $b){ return $a['priority'] <=> $b['priority']; });
        return $mx_addresses;
    }
    // -------------------------------------
    // END Get DNS Records
    // -------------------------------------


    // DNSCHECK

    function get_default_dns($toproof, $nameserverlist) {
        $dns_array_result = [];

        foreach ($nameserverlist as $nameserver) {
            $ns_ip   = $nameserver['ns_ip']   ?? '';
            $ns_name = $nameserver['ns_name'] ?? '';
            $type    = $nameserver['type']    ?? '';

            $ipv4_addresses = get_a_records($toproof, $ns_ip);
            $ipv6_addresses = get_aaaa_records($toproof, $ns_ip);
            $txt_adresses   = get_txt_records($toproof, $ns_ip);
            $cname_adresses = get_cname_records($toproof, $ns_ip);
            $mx_addresses   = get_mx_records($toproof, $ns_ip);

            $dns_array_result[] = [
                'ns_ip'   => $ns_ip,
                'ns_name' => $ns_name,
                'type'    => $type,
                'ipv4'    => $ipv4_addresses,
                'ipv6'    => $ipv6_addresses,
                'txt'     => $txt_adresses,
                'cname'   => $cname_adresses,
                'mx'      => $mx_addresses,
            ];
        }

        return $dns_array_result;
    }

    function check_dns_divergences($dns_array) {

        if (empty($dns_array)) {
            return false;
        }

        $ipv4_are_same = true;
        $ipv6_are_same = true;
        $txt_are_same = true;
        $cname_are_same = true;
        $mx_are_same = true;

        $reference_ipv4 = $dns_array[0]['ipv4'];
        $reference_ipv6 = $dns_array[0]['ipv6'];
        $reference_txt = $dns_array[0]['txt'];
        $reference_cname = $dns_array[0]['cname'];
        $reference_mx = $dns_array[0]['mx'];

        foreach ($dns_array as $nameserver) {
            if ($nameserver['ipv4']  !== $reference_ipv4)  $ipv4_are_same = false;
            if ($nameserver['ipv6']  !== $reference_ipv6)  $ipv6_are_same = false;
            if ($nameserver['txt']   !== $reference_txt)   $txt_are_same = false;
            if ($nameserver['cname'] !== $reference_cname) $cname_are_same = false;
            if ($nameserver['mx']    !== $reference_mx)    $mx_are_same = false;
        }

        if ($ipv4_are_same === false) {
            echo '<div class="alert alert-warning"><strong>Note!</strong> The ipv4 entries differ from nameserver to nameserver.</div>';
        }
        if ($ipv6_are_same === false) {
            echo '<div class="alert alert-warning"><strong>Note!</strong> The ipv6 entries differ from nameserver to nameserver.</div>';
        }
        if ($txt_are_same === false) {
            echo '<div class="alert alert-warning"><strong>Note!</strong> The txt entries differ from nameserver to nameserver.</div>';
        }
        if ($cname_are_same === false) {
            echo '<div class="alert alert-warning"><strong>Note!</strong> The cname entries differ from nameserver to nameserver.</div>';
        }
        if ($mx_are_same === false) {
            echo '<div class="alert alert-warning"><strong>Note!</strong> The mx entries differ from nameserver to nameserver.</div>';
        }
    }


    function print_dns_check($dns_array) {
        foreach ($dns_array as $result) {
            $ns_ip   = $result['ns_ip'];
            $ns_name = $result['ns_name'];
            $ns_type = $result['type'];
            $ipv4    = $result['ipv4'];
            $ipv6    = $result['ipv6'];
            $txt     = $result['txt'];
            $cname   = $result['cname'];
            $mx      = $result['mx'];

            if ($ns_type === "authorative") {
                echo '<div class="content-header">' . h($ns_name) . ' (' . h($ns_ip) . ') (Authorative)</div>';
            } else {
                echo '<div class="content-header">' . h($ns_name) . ' (' . h($ns_ip) . ')</div>';
            }

            echo '<table class="table table-borderless">';
            echo '<thead class="table-head-background table-head-bigfont">';
            echo '<tr>';
            echo '<th>IPv4';
            ?>
                <button type="button" class="btn table-info btn-sm" data-bs-toggle="modal" data-bs-target="#aModal">
                    <img src="assets/feather/info.svg" alt="Info" width="16" height="16">
                </button>
            <?php
            echo '</th>';
            echo '<th>';
            echo '</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            if (empty($ipv4)) {
                echo '<tr><th>-</th><th></th></tr>';
            } else {
                foreach ($ipv4 as $ipv4_entry) {
                    echo '<tr>';
                    echo '<th>';
                    ?>
                        <form action="index.php" method="get">
                            <?php echo h($ipv4_entry); ?>
                            <input type="hidden" name="toproof" value="<?php echo h($ipv4_entry); ?>">
                            <button type="submit" class="btn table-info btn-sm" name="action" value="whois">
                                <img src="assets/feather/eye.svg" alt="WHOIS" width="16" height="16">
                            </button>
                            <button type="submit" class="btn table-info btn-sm" name="action" value="geo">
                                <img src="assets/feather/map-pin.svg" alt="GEO" width="16" height="16">
                            </button>
                        </form>
                    <?php
                    echo '</th>';
                    echo '<th>';
                    echo '</th>';
                    echo '</tr>';
                }
            }
            echo '</tbody>';

            // IPv6
            echo '<thead class="table-head-background table-head-bigfont">';
            echo '<tr>';
            echo '<th>IPv6';
            ?>
                <button type="button" class="btn table-info btn-sm" data-bs-toggle="modal" data-bs-target="#aaaaModal">
                    <img src="assets/feather/info.svg" alt="Info" width="16" height="16">
                </button>
            <?php
            echo '<th>';
            echo '</th>';
            echo '</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            if (empty($ipv6)) {
                echo '<tr><th>-</th><th></th></tr>';
            } else {
                foreach ($ipv6 as $ipv6_entry) {
                    echo '<tr>';
                    echo '<th>';
                    ?>
                        <form action="index.php" method="get">
                            <?php echo h($ipv6_entry); ?>
                            <input type="hidden" name="toproof" value="<?php echo h($ipv6_entry); ?>">
                            <button type="submit" class="btn table-info btn-sm" name="action" value="whois">
                                <img src="assets/feather/eye.svg" alt="WHOIS" width="16" height="16">
                            </button>
                            <button type="submit" class="btn table-info btn-sm" name="action" value="geo">
                                <img src="assets/feather/map-pin.svg" alt="GEO" width="16" height="16">
                            </button>
                        </form>
                    <?php
                    echo '</th>';
                    echo '<th>';
                    echo '</th>';
                    echo '</tr>';
                }
            }
            echo '</tbody>';

            // TXT
            echo '<thead class="table-head-background table-head-bigfont">';
            echo '<tr>';
            echo '<th>TXT';
            ?>
                <button type="button" class="btn table-info btn-sm" data-bs-toggle="modal" data-bs-target="#txtModal">
                    <img src="assets/feather/info.svg" alt="Info" width="16" height="16">
                </button>
            <?php
            echo '</th>';
            echo '<th>';
            echo '</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            if (empty($txt)) {
                echo '<tr><th>-</th><th></th></tr>';
            } else {
                foreach ($txt as $txt_entry) {
                    echo '<tr>';
                    echo '<th>' . h($txt_entry) . '</th>';
                    echo '<th></th>';
                    echo '</tr>';
                }
            }
            echo '</tbody>';

            // CNAME
            echo '<thead class="table-head-background table-head-bigfont">';
            echo '<tr>';
            echo '<th>CNAME';
            ?>
                <button type="button" class="btn table-info btn-sm" data-bs-toggle="modal" data-bs-target="#cnameModal">
                    <img src="assets/feather/info.svg" alt="Info" width="16" height="16">
                </button>
            <?php
            echo '</th>';
            echo '<th>';
            echo '</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            if (empty($cname)) {
                echo '<tr><th>-</th><th></th></tr>';
            } else {
                foreach ($cname as $cname_entry) {
                    echo '<tr>';
                    echo '<th>';
                    ?>
                        <form action="index.php" method="get">
                            <?php echo h($cname_entry); ?>
                            <input type="hidden" name="toproof" value="<?php echo h($cname_entry); ?>">
                            <button type="submit" class="btn table-info btn-sm" name="action" value="geo">
                                <img src="assets/feather/map-pin.svg" alt="GEO" width="16" height="16">
                            </button>
                        </form>
                    <?php
                    echo '</th>';
                    echo '<th></th>';
                    echo '</tr>';
                }
            }
            echo '</tbody>';

            // MX
            echo '<thead class="table-head-background table-head-bigfont">';
            echo '<tr>';
            echo '<th>MX';
            ?>
                <button type="button" class="btn table-info btn-sm" data-bs-toggle="modal" data-bs-target="#mxModal">
                    <img src="assets/feather/info.svg" alt="Info" width="16" height="16">
                </button>
            <?php
            echo '</th>';
            echo '<th>';
            echo '</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<thead class="table-head-background table-head-smallfont">';
            echo '<tr>';
            echo '<th>Destination</th>';
            echo '<th>Prio</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            if (empty($mx)) {
                echo '<tr><th>-</th><th></th></tr>';
            } else {
                foreach ($mx as $mx_entry) {
                    $priority = (int)$mx_entry['priority'];
                    $destination = $mx_entry['destination'];
                    echo '<tr>';
                    echo "<th>";
                    ?>
                        <form action="index.php" method="get">
                            <?php echo h($destination); ?>
                            <input type="hidden" name="toproof" value="<?php echo h($destination); ?>">
                            <button type="submit" class="btn table-info btn-sm" name="action" value="dnscheck">
                                <img src="assets/feather/globe.svg" alt="GEO" width="16" height="16">
                            </button>
                        </form>
                    <?php
                    echo '</th>';
                    echo '<th>' . (int)$priority . '</th>';
                    echo '</tr>';
                }
            }
            echo '</tbody>';
            echo '</table>';

            // MODAL with explanation of dns-records
            ?>
            <div class="modal" id="mxModal">
                <div class="modal-dialog">
                    <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">MX-Records</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                    <?php

                        mxExplain();

                    ?>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                    </div>

                    </div>
                </div>
            </div>

            <div class="modal" id="aaaaModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">AAAA-Records</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                    <?php aaaaExplain(); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                    </div>
                    </div>
                </div>
            </div>

            <div class="modal" id="aModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">A-Records</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                    <?php aExplain(); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                    </div>
                    </div>
                </div>
            </div>

            <div class="modal" id="cnameModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">CNAME-Records</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                    <?php cnameExplain(); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                    </div>
                    </div>
                </div>
            </div>

            <div class="modal" id="nsModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">NS-Records</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                    <?php nsExplain(); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                    </div>
                    </div>
                </div>
            </div>

            <div class="modal" id="txtModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">TXT-Records</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                    <?php txtExplain(); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                    </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }

    function arrayToString($array) {
        $result = [];
        foreach ($array as $value) {
            if (is_array($value)) {
                $result[] = arrayToString($value);
            } else {
                $result[] = $value;
            }
        }
        return implode(', ', $result);
    }
?>
