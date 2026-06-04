<?php

    require_once 'src/helper.php';

    function rdns_ptr($toproof) {

        $type = whatisit($toproof);

        if ($type === "IP") {

            if (!is_safe_ip($toproof)) {
                echo '<div class="alert alert-warning"><strong>Note!</strong> Invalid IP.</div>';
                return;
            }

            echo '<div class="content-header">rDNS / PTR Check</div>';

            $get_rdns = get_rdns_of_ip($toproof);
            if (!empty($get_rdns)) {

                foreach ($get_rdns as $rdns_entry) {

                    $rdns_to_check = remove_dot_if_last_char($rdns_entry);
                    if (!is_safe_domain($rdns_to_check)) {
                        continue;
                    }

                    $tld = get_tld($rdns_to_check);
                    if ($tld === "none") {
                        continue;
                    }

                    $nameservers = get_authorative_nameservers($rdns_to_check, $tld);
                    $nameserver_ip = false;

                    if (!empty($nameservers)) {
                        $first_nameserver = $nameservers[0];
                        if (isset($first_nameserver['ns_ip']) && is_safe_ip($first_nameserver['ns_ip'])) {
                            $nameserver_ip = $first_nameserver['ns_ip'];
                        }
                    }

                    if ($nameserver_ip !== false) {

                        $ipv4_entries = get_a_records($rdns_to_check, $nameserver_ip);

                        if (!empty($ipv4_entries)) {
                            foreach ($ipv4_entries as $ipv4_adress) {
                                if ($ipv4_adress === $toproof) {
                                    echo 'The IP address '. h($toproof) . ' points to ' . h($rdns_to_check) . ' and this in turn points to the IP address ' . h($toproof) . '  ';
                                    echo '<span class="badge bg-success">rDNS</span>';
                                    echo '<span class="badge bg-success">DNS</span>';
                                    echo '<span class="badge bg-success">PTR</span>';
                                } else {
                                    echo 'The IP address '. h($toproof) . ' points to ' . h($rdns_to_check) . '. But the domain is not on ' . h($toproof) . '  ';
                                    echo '<span class="badge bg-success">rDNS</span>';
                                    echo '<span class="badge bg-danger">DNS</span>';
                                    echo '<span class="badge bg-danger">PTR</span>';
                                }
                            }
                        }
                    }

                    echo '<br/><br/>';
                }
            } else {
                echo 'No rDNS entries were found for the ip ' . h($toproof) . '  ';
                echo '<span class="badge bg-danger">rDNS</span>';
                echo '<span class="badge bg-danger">DNS</span>';
                echo '<span class="badge bg-danger">PTR</span>';
            }


        } elseif ($type === "Domain") {

            if (!is_safe_domain($toproof)) {
                echo '<div class="alert alert-warning"><strong>Note!</strong> Invalid domain.</div>';
                return;
            }

            echo '<div class="content-header">rDNS / PTR Check</div>';

            $tld = get_tld($toproof);
            if ($tld === "none") {
                echo '<div class="alert alert-warning"><strong>Note!</strong> TLD unknown.</div>';
                return;
            }

            $nameservers = get_authorative_nameservers($toproof, $tld);
            $nameserver_ip = false;
            if (!empty($nameservers)) {
                $first_nameserver = $nameservers[0];
                if (isset($first_nameserver['ns_ip']) && is_safe_ip($first_nameserver['ns_ip'])) {
                    $nameserver_ip = $first_nameserver['ns_ip'];
                }
            }

            if ($nameserver_ip !== false) {
                $ipv4_entries = get_a_records($toproof, $nameserver_ip);
                if (!empty($ipv4_entries)) {

                    foreach ($ipv4_entries as $ip_adress) {

                        if (!is_safe_ip($ip_adress)) continue;

                        $get_rdns = get_rdns_of_ip($ip_adress);
                        if (!empty($get_rdns)) {

                            foreach ($get_rdns as $rdns_entry) {
                                // Normalise both sides for comparison —
                                // DNS is case-insensitive (RFC 4343) and
                                // dig may return either form.
                                $rdns_to_check = strtolower(remove_dot_if_last_char($rdns_entry));
                                $compare_to    = strtolower($toproof);

                                if ($rdns_to_check === $compare_to) {
                                    echo 'The Domain '. h($toproof) . ' points to ' . h($ip_adress) . ' and this in turn points to the Domain ' . h($toproof) . '  ';
                                    echo '<span class="badge bg-success">rDNS</span>';
                                    echo '<span class="badge bg-success">DNS</span>';
                                    echo '<span class="badge bg-success">PTR</span>';
                                } else {
                                    echo 'The Domain '. h($toproof) . ' points to ' . h($ip_adress) . '. But this in turn points not to the Domain ' . h($toproof) . '  ';
                                    echo '<span class="badge bg-danger">rDNS</span>';
                                    echo '<span class="badge bg-success">DNS</span>';
                                    echo '<span class="badge bg-danger">PTR</span>';
                                }
                            }
                        }

                        echo '<br/><br/>';
                    }
                }
            } else {
                echo 'No IPv4 entries were found for the domain ' . h($toproof) . '  ';
                echo '<span class="badge bg-danger">rDNS</span>';
                echo '<span class="badge bg-danger">DNS</span>';
                echo '<span class="badge bg-danger">PTR</span>';
            }
        }
    }

    /**
     * Reverse-DNS lookup using `dig -x`.
     * The IP must be validated before this is called.
     */
    function get_rdns_of_ip($ip_adress) {
        $rdns_domains = [];

        if (!is_safe_ip($ip_adress)) {
            return $rdns_domains;
        }

        // Use escapeshellarg + a strictly validated IP. We do NOT need
        // the trailing | sort that the original had — we sort in PHP.
        $command = 'dig -x ' . escapeshellarg($ip_adress) . ' +short';
        $output = shell_exec($command);
        if ($output === null) {
            return $rdns_domains;
        }

        foreach (explode("\n", $output) as $rdns_entry) {
            $rdns_entry = trim($rdns_entry);
            if ($rdns_entry !== '') {
                $rdns_domains[] = $rdns_entry;
            }
        }
        sort($rdns_domains);
        return $rdns_domains;
    }

?>
