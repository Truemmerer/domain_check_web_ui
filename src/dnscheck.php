<?php

    /** 
     * Checking the DNS records of a domain
     * @param $toproof - Domain
    */

    require_once 'src/dnsexplain.php';

    function dns_check($toproof, $tld) {

        // Get authorative nameservers
        $authorativelist = get_authorative_nameservers($toproof, $tld);

        // Import the nameserverlist.yaml as array
        $yamlContent = file_get_contents("nameservers.yaml");
        $customns = yaml_parse($yamlContent);

        // convert custom nameservers in correct format
        $custom_ns_format = array();
        foreach ($customns as $name => $ip) {
            $custom_ns_format[] = array(
                "ns_name" => $name,
                "ns_ip" => $ip,
                "type" => "custom"
            );
        }

        // Merge authorative nameservers and custom nameservers to one array
        $nameservers = array_merge($authorativelist, $custom_ns_format);


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
    
    // get A records
    function get_a_records($toproof, $ns_ip) {
        
        $ipv4_addresses = [];
        
        $command = "dig +short A " . escapeshellcmd($toproof) . " @" . escapeshellcmd($ns_ip) . " | sort";
        $output = shell_exec($command);
        $ipv4_a = explode("\n", $output);
        foreach ($ipv4_a as $ipv4_entry) {
            if (trim($ipv4_entry) !== '') {
                $ipv4_addresses[] = $ipv4_entry;
            }
        }
        sort($ipv4_addresses); //Sort entries for better readability
        return $ipv4_addresses;
    }

    // get AAAA records
    function get_aaaa_records($toproof, $ns_ip) {

        $ipv6_addresses = [];

        $command = "dig +short AAAA " . escapeshellcmd($toproof) . " @" . escapeshellcmd($ns_ip) . " | sort";
        $output = shell_exec($command);
        $ipv6_a = explode("\n", $output);
        foreach ($ipv6_a as $ipv6_entry) {
            if (trim($ipv6_entry) !== '') {
                $ipv6_addresses[] = $ipv6_entry;
            }
        }
        sort($ipv6_addresses); //Sort entries for better readability
        return $ipv6_addresses;
    }

    // get TXT records
    function get_txt_records($toproof, $ns_ip) {

        $txt_adresses = [];

        $command = "dig +short TXT " . escapeshellcmd($toproof) . " @" . escapeshellcmd($ns_ip) . " | sort";
        $output = shell_exec($command);
        $txt_a = explode("\n", $output);
        foreach ($txt_a as $txt_entry) {
            if (trim($txt_entry) !== '') {
                $txt_adresses[] = $txt_entry;
            }
        }
        sort($txt_adresses); //Sort entries for better readability
        return $txt_adresses;
    }

    // get CNAME records
    function get_cname_records($toproof, $ns_ip) {

        $cname_adresses = [];

        $command = "dig +short CNAME " . escapeshellcmd($toproof) . " @" . escapeshellcmd($ns_ip) . " | sort";
        $output = shell_exec($command);
        $cname_a = explode("\n", $output);
        foreach ($cname_a as $cname_entry) {
            if (trim($cname_entry) !== '') {
                $cname_adresses[] = $cname_entry;
            }
        }
        sort($cname_adresses); //Sort entries for better readability
        return $cname_adresses;
    }

    // get mx records
    function get_mx_records($toproof, $ns_ip) {

        $mx_addresses = [];

        $command = "dig +short MX " . escapeshellcmd($toproof) . " @" . escapeshellcmd($ns_ip) . " | sort";
        $output = shell_exec($command);
        $mx_a = explode("\n", $output);
        foreach ($mx_a as $mx_entry) {
            if (trim($mx_entry) !== '') {
                $parts = explode(' ', $mx_entry);
                $priority = (int)$parts[0];
                $destination = $parts[1];
                //remove last dot of string. example.com. -> example.com
                $destination_without_dot = remove_dot_if_last_char($destination);
                $mx_addresses[] = array('priority' => $priority, 'destination' => $destination_without_dot);
            }
        }
        sort($mx_addresses); //Sort entries for better readability
        return $mx_addresses;
    }
    // -------------------------------------
    // END Get DNS Records
    // -------------------------------------


    // DNSCHECK

    function get_default_dns($toproof, $nameserverlist) {
        $dns_array_result = [];

        foreach ($nameserverlist as $nameserver) {

            $ns_ip = $nameserver['ns_ip'];
            $ns_name = $nameserver['ns_name'];
            $type = $nameserver['type'];

            // get default DNS records (A; AAAA; TXT; CNAME; MX)
            $ipv4_addresses = get_a_records($toproof, $ns_ip);
            $ipv6_addresses = get_aaaa_records($toproof, $ns_ip);
            $txt_adresses = get_txt_records($toproof, $ns_ip);
            $cname_adresses = get_cname_records($toproof, $ns_ip);
            $mx_addresses = get_mx_records($toproof, $ns_ip);

            $dns_array_result[] = [
                'ns_ip' => $ns_ip,
                'ns_name' => $ns_name,
                'type' => $type,
                'ipv4' => $ipv4_addresses,
                'ipv6' => $ipv6_addresses,
                'txt' => $txt_adresses,
                'cname' => $cname_adresses,
                'mx' => $mx_addresses,
            ];

        } // END of foreach


        return $dns_array_result;
    }

    function check_dns_divergences($dns_array) {

        // check if array is empty
        if (empty($dns_array)) {
            return false;
        }

        $ipv4_are_same = true;
        $ipv6_are_same = true;
        $txt_are_same = true;
        $cname_are_same = true;
        $mx_are_same = true;

        // Reference entrys
        $reference_ipv4 = $dns_array[0]['ipv4'];
        $reference_ipv6 = $dns_array[0]['ipv6'];
        $reference_txt = $dns_array[0]['txt'];
        $reference_cname = $dns_array[0]['cname'];
        $reference_mx = $dns_array[0]['mx'];

        foreach ($dns_array as $nameserver) {

            // current entrys
            $current_ipv4 = $nameserver['ipv4'];
            $current_ipv6 = $nameserver['ipv6'];
            $current_txt = $nameserver['txt'];
            $current_cname = $nameserver['cname'];
            $current_mx = $nameserver['mx'];
       
            //check if entrys are same
            if ($current_ipv4 !== $reference_ipv4) {
                $ipv4_are_same = false;
            }
            if ($current_ipv6 !== $reference_ipv6) {
                $ipv6_are_same = false;
            }
            if ($current_txt !== $reference_txt) {
                $txt_are_same = false;
            }
            if ($current_cname !== $reference_cname) {
                $cname_are_same = false;
            }
            if ($current_mx !== $reference_mx) {
                $mx_are_same = false;
            }
        }

        // report a error if nameserver_arrays are not the same 
        if ($ipv4_are_same === false) {
            echo '<div class="alert alert-warning">';
                echo '<strong>Note!</strong> The ipv4 entries differ from nameserver to nameserver.</a>';
            echo '</div>';
        }
        if ($ipv6_are_same === false) {
            echo '<div class="alert alert-warning">';
                echo '<strong>Note!</strong> The ipv6 entries differ from nameserver to nameserver.</a>';
            echo '</div>';
        }
        if ($txt_are_same === false) {
            echo '<div class="alert alert-warning">';
                echo '<strong>Note!</strong> The txt entries differ from nameserver to nameserver.</a>';
            echo '</div>';
        }
        if ($cname_are_same === false) {
            echo '<div class="alert alert-warning">';
                echo '<strong>Note!</strong> The cname entries differ from nameserver to nameserver.</a>';
            echo '</div>';
        }
        if ($mx_are_same === false) {
            echo '<div class="alert alert-warning">';
                echo '<strong>Note!</strong> The mx entries differ from nameserver to nameserver.</a>';
            echo '</div>';
        }


    }


    function print_dns_check($dns_array) {
        foreach ($dns_array as $result) {
            $ns_ip = $result['ns_ip'];
            $ns_name = $result['ns_name'];
            $ns_type = $result['type'];
            $ipv4 = $result['ipv4'];
            $ipv6 = $result['ipv6'];
            $txt = $result['txt'];
            $cname = $result['cname'];
            $mx = $result['mx'];
            // $ns = $result['ns'];
            
            if ($ns_type === "authorative") {
                echo '<div class="content-header">' . $ns_name . ' (' . $ns_ip . ') (Authorative)</div>';
            } else {
                echo '<div class="content-header">' . $ns_name . ' (' . $ns_ip . ')</div>';
            }

            echo '<table class="table table-borderless">';
            echo '<thead class="table-head-background table-head-bigfont">';
            echo '<tr>';
            echo '<th>IPv4';
            ?>
                <button type="button" class="btn table-info btn-sm" data-bs-toggle="modal" data-bs-target="#aModal">
                    <img src="assets/feather/info.svg" alt="Info" width="32" height="16">
                </button>
            <?php
            echo '</th>';
            echo '<th>';
            echo '</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            if (empty($ipv4)) {
                echo '<tr>';
                echo "<th>-</th>";
                echo '<th></th>';
                echo '</tr>';
            } else {
                foreach ($ipv4 as $ipv4_entry) {
                    echo '<tr>';
                    echo '<th>'; 
                    ?>
                        <form action="index.php" method="get">
                            <?php echo $ipv4_entry; ?>
                            <input type="hidden" name="toproof" value="<?php echo $ipv4_entry; ?>">
                            <button type="submit" class="btn table-info btn-sm" name="action" value="geo">
                                <img src="assets/feather/map-pin.svg" alt="GEO" width="32" height="16">
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
                    <img src="assets/feather/info.svg" alt="Info" width="32" height="16">
                </button>
            <?php
            echo '<th>';
            echo '</th>';
            echo '</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            if (empty($ipv6)) {
                echo '<tr>';
                echo "<th>-</th>";
                echo '<th></th>';
                echo '</tr>';
            } else {
                foreach ($ipv6 as $ipv6_entry) {
                    echo '<tr>';
                    echo '<th>'; 
                    ?>
                        <form action="index.php" method="get">
                            <?php echo $ipv6_entry; ?>
                            <input type="hidden" name="toproof" value="<?php echo $ipv6_entry; ?>">
                            <button type="submit" class="btn table-info btn-sm" name="action" value="geo">
                                <img src="assets/feather/map-pin.svg" alt="GEO" width="32" height="16">
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
                    <img src="assets/feather/info.svg" alt="Info" width="32" height="16">
                </button>
            <?php
            echo '</th>';
            echo '<th>';
            echo '</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            if (empty($txt)) {
                echo '<tr>';
                echo "<th>-</th>";
                echo '<th></th>';
                echo '</tr>';
            } else {
                foreach ($txt as $txt_entry) {
                    echo '<tr>';
                    echo "<th>" . $txt_entry . "</th>";
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
                    <img src="assets/feather/info.svg" alt="Info" width="32" height="16">
                </button>
            <?php
            echo '</th>';
            echo '<th>';
            echo '</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            if (empty($cname)) {
                echo '<tr>';
                echo "<th>-</th>";
                echo '<th></th>';
                echo '</tr>';
            } else {
                foreach ($cname as $cname_entry) {
                    echo '<tr>';
                    echo "<th>";
                    ?>
                        <form action="index.php" method="get">
                            <?php echo $ipv6_entry; ?>
                            <input type="hidden" name="toproof" value="<?php echo $cname_entry; ?>">
                            <button type="submit" class="btn table-info btn-sm" name="action" value="geo">
                                <img src="assets/feather/map-pin.svg" alt="GEO" width="32" height="16">
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
                    <img src="assets/feather/info.svg" alt="Info" width="32" height="16">
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
                echo '<tr>';
                echo "<th>-</th>";
                echo '<th></th>';
                echo '</tr>';
            } else {
                foreach ($mx as $mx_entry) {
                    $priority = $mx_entry['priority'];
                    $destination = $mx_entry['destination'];
                    echo '<tr>';
                    echo "<th>";
                                        ?>
                        <form action="index.php" method="get">
                            <?php echo $destination; ?>
                            <input type="hidden" name="toproof" value="<?php echo $destination; ?>">
                            <button type="submit" class="btn table-info btn-sm" name="action" value="dnscheck">
                                <img src="assets/feather/globe.svg" alt="GEO" width="32" height="16">
                            </button>
                        </form>
                    <?php
                    echo '</th>';
                    echo '<th>' . $priority . '</th>';
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

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">AAAA-Records</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                    <?php

                        aaaaExplain();

                    ?>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                    </div>
                    
                    </div>
                </div>
            </div>

            <div class="modal" id="aModal">
                <div class="modal-dialog">
                    <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">A-Records</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                    <?php

                        aExplain();

                    ?>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                    </div>
                    
                    </div>
                </div>
            </div>

            <div class="modal" id="cnameModal">
                <div class="modal-dialog">
                    <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">CNAME-Records</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                    <?php

                        cnameExplain();

                    ?>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                    </div>
                    
                    </div>
                </div>
            </div>

            <div class="modal" id="nsModal">
                <div class="modal-dialog">
                    <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">NS-Records</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                    <?php

                        nsExplain();

                    ?>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                    </div>
                    
                    </div>
                </div>
            </div>

            <div class="modal" id="txtModal">
                <div class="modal-dialog">
                    <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">TXT-Records</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                    <?php

                        txtExplain();

                    ?>
                    </div>

                    <!-- Modal footer -->
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
        $result = array();
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
