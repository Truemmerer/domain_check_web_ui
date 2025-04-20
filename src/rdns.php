<?php

    require_once 'src/helper.php';

    function rdns_ptr($toproof) {

        $type = whatisit($toproof);

        if ($type === "IP") {

            echo '<div class="content-header">rDNS / PTR Check</div>';

            $get_rdns = get_rdns_of_ip($toproof);
            if (!empty($get_rdns)) {
                
                foreach ($get_rdns as $rdns_entry) {
                  
                    $rdns_to_check = remove_dot_if_last_char($rdns_entry);
                    $tld = get_tld($rdns_to_check);
                    $nameservers = get_authorative_nameservers($rdns_to_check, $tld);
                    $nameserver_ip = false;

                    if (!empty($nameservers)) {
                        $first_nameserver = $nameservers[0];
                        if (isset($first_nameserver['ns_ip'])) {
                            $nameserver_ip = $first_nameserver['ns_ip'];
                        }
                    }

                    if ($nameserver_ip !== false) {

                        $ipv4_entries = get_a_records($rdns_to_check, $nameserver_ip);

                        if (!empty($ipv4_entries)) {
                            foreach ($ipv4_entries as $ipv4_adress) {
                                if ($ipv4_adress === $toproof) {
                                    echo 'The IP adress '. $toproof . ' points to ' . $rdns_to_check . ' and this in turn points to the IP address ' . $toproof . '  ';
                                    echo '<span class="badge bg-success">rDNS</span>';  
                                    echo '<span class="badge bg-success">DNS</span>';  
                                    echo '<span class="badge bg-success">PTR</span>';  
                                } else {
                                    echo 'The IP adress '. $toproof . ' points to ' . $rdns_to_check . '. But the domain is not on ' . $toproof . '  ';
                                    echo '<span class="badge bg-success">rDNS</span>';  
                                    echo '<span class="badge bg-danger">DNS</span>';  
                                    echo '<span class="badge bg-danger">PTR</span>';  
                                }
                            }
                        }
                    }

                }
            } else {
                echo 'No rDNS entries were found for the ip ' . $toproof . '  ';
                echo '<span class="badge bg-danger">rDNS</span>';  
                echo '<span class="badge bg-danger">DNS</span>';  
                echo '<span class="badge bg-danger">PTR</span>';  
            }


        } elseif ($type === "Domain") {

            echo '<div class="content-header">rDNS / PTR Check</div>';


            $tld = get_tld($toproof);

            // get Domain if entry is a subdomain
            $nameservers = get_authorative_nameservers($toproof, $tld);
            $nameserver_ip = false;
            if (!empty($nameservers)) {
                $first_nameserver = $nameservers[0];
                if (isset($first_nameserver['ns_ip'])) {
                    $nameserver_ip = $first_nameserver['ns_ip'];
                }
            }

            if ($nameserver_ip !== false) {
                    // get A Records
                $ipv4_entries = get_a_records($toproof, $nameserver_ip);
                if (!empty($ipv4_entries)) {

                    foreach ($ipv4_entries as $ip_adress) {

                        $get_rdns = get_rdns_of_ip($ip_adress);
                        if (!empty($get_rdns)) {

                            foreach ($get_rdns as $rdns_entry) {
                                $rdns_to_check = remove_dot_if_last_char($rdns_entry);
                                
                                if ($rdns_to_check === $toproof) {
                                    echo 'The Domain '. $toproof . ' points to ' . $ip_adress . ' and this in turn points to the Domain ' . $toproof . '  ';
                                    echo '<span class="badge bg-success">rDNS</span>';  
                                    echo '<span class="badge bg-success">DNS</span>';  
                                    echo '<span class="badge bg-success">PTR</span>';  
                                } else {
                                    echo 'The Domain '. $toproof . ' points to ' . $ip_adress . '. But this in turn points not to the Domain ' . $toproof . '  ';
                                    echo '<span class="badge bg-danger">rDNS</span>';  
                                    echo '<span class="badge bg-success">DNS</span>';  
                                    echo '<span class="badge bg-danger">PTR</span>';  

                                }
                            }

                        }

                    }


                }
            } else {
                echo 'No IPv4 entries were found for the domain ' . $toproof . '  ';
                echo '<span class="badge bg-danger">rDNS</span>';  
                echo '<span class="badge bg-danger">DNS</span>';  
                echo '<span class="badge bg-danger">PTR</span>';  
            }


        }
    }

    function get_rdns_of_ip($ip_adress) {
        $command = 'dig -x ' . $ip_adress . ' +short | sort';

        $output = shell_exec($command);
        $rdns_array = explode("\n", $output);

        foreach ($rdns_array as $rdns_entry) {
            if (trim($rdns_entry) !== '') {
                $rdns_domains[] = $rdns_entry;
            }
        }
        return $rdns_domains;
    }


?>
