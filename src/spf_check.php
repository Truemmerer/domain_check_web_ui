<?php

    /** 
     * Sender Policy Framework (SPF) check
     * @param $toproof - Domain
     */

      require_once 'src/dnscheck.php';

     function spf_check($toproof) {

        // v=spf1 mx a include:_spf.webhosting.systems ~all

        // Get TXT Records from Domain
        $command = "dig +short TXT " . escapeshellarg($toproof) . " | sort -u";
        $output = shell_exec($command);
        $txt_records = explode("\n", $output);
        $spf_records = [];

        $tld = get_tld($toproof);
        $nameservers = get_authorative_nameservers($toproof, $tld);
        $nameserver_ip = false;
        if (!empty($nameservers)) {
            $first_nameserver = $nameservers[0];
            if (isset($first_nameserver['ns_ip'])) {
                $nameserver_ip = $first_nameserver['ns_ip'];
            }
        }

        echo '<div class="content-header">The following SPF records were found</div>';
        foreach ($txt_records as $txt_record) {
            // remove " from string
            $txt_record = trim($txt_record, '"');

            // Check if string begins with 'v=spf1'
            if (preg_match('/^v=spf1/', $txt_record)) {
                $spf_records[] = $txt_record;    
            }
           
        }

        foreach ($spf_records as $spf_record) {
            // remove 'v=spf1' from string
            //$spf_record = trim($spf_record, 'v=spf1');

            // Output
            $parts = explode(' ', $spf_record);

            echo $spf_record;

            echo '<table>';

            foreach ($parts as $part) {
                echo '<tr>';
                if (strpos($part, 'v=') === 0) {
                    echo '<td>';
                    echo 'SPF Version:';
                    echo '</td><td>';
                    echo substr($part, 2);
                    echo '</td>';
                } elseif (strpos($part, 'a') === 0) {
                    echo '<td>';
                    echo '</br>';
                    echo 'Mechanism:';
                    echo '</td><td>';
                    echo '</br>';
                    echo 'a (Allow mail from the domain\'s A records):';
                    echo '</td>';
                    if ($nameserver_ip !== false) {
                        $ipv4_entries = get_a_records($toproof, $nameserver_ip);
                   
                        if (!empty($ipv4_entries)) {
                            foreach ($ipv4_entries as $entry) {
                                echo '<tr><td></td><td>';
                                echo htmlspecialchars($entry); // Use htmlspecialchars to prevent XSS
                                echo '</td></tr>';
                            }
                        } else {
                            echo '<tr><td></td><td>No IPv4 entries found</td></tr>'; // Handle case where no entries are found
                        }
                    }
                } elseif (strpos($part, 'mx') === 0) {
                    echo '<td>';
                    echo '</br>';
                    echo 'Mechanism:';
                    echo '</td><td>';
                    echo '</br>';
                    echo 'mx (Allow mail from the domain\'s MX records):';
                    echo '</td>';
                    if ($nameserver_ip !== false) {
                        $mx_entries = get_mx_records($toproof, $nameserver_ip);
                   
                        if (!empty($mx_entries)) {
                            foreach ($mx_entries as $entry) {
                                $destination = $entry['destination'];
                                echo '<tr><td></td><td>';
                                echo $destination; 
                                echo '</td></tr>';
                            }
                        } else {
                            echo '<tr><td></td><td>No MX entries found</td></tr>'; // Handle case where no entries are found
                        }
                    }
                } elseif (strpos($part, 'ip4:') === 0) {
                    echo '<td>';
                    echo '</br>';
                    echo 'Mechanism:';
                    echo '</td><td>';
                    echo '</br>';
                    echo 'ip4 (Allow mail from the specified IPv4 address)';
                    echo '</td>';
                    echo '<tr><td></td><td>';
                    echo substr($part, 4);
                    echo '</td>';
                } elseif (strpos($part, 'ip6:') === 0) {
                    echo '<td>';
                    echo '</br>';
                    echo 'Mechanism:';
                    echo '</td><td>';
                    echo '</br>';
                    echo 'ip6 (Allow mail from the specified IPv6 address)';
                    echo '</td>';
                    echo '<tr><td></td><td>';
                    echo substr($part, 4);
                    echo '</td>';
                } elseif (strpos($part, 'ptr') === 0) {
                    echo '<td>';
                    echo '</br>';
                    echo 'Mechanism:';
                    echo '</td><td>';
                    echo '</br>';
                    echo 'ptr (Matches if the DNS reverse-lookup for the IP address matches the given domain)';
                    echo '</td>';
                    echo '<tr><td></td><td>';
                    echo substr($part, 4);
                    echo '</td>';                
                } elseif (strpos($part, 'include:') === 0) {
                    echo '<td>';
                    echo '</br>';
                    echo 'Mechanism:';
                    echo '</td><td>';
                    echo '</br>';
                    echo 'include (Include the SPF record from the specified domain)';
                    echo '</td><tr><td></td><td>';
                    echo substr($part, 8);
                    echo '</td>';
                } elseif (strpos($part, '-all') === 0) {
                    echo '<td>';
                    echo '</br>';
                    echo 'Default Policy:';
                    echo '</td><td>';
                    echo '</br>';
                    echo '-all (Reject all mail that doesn\'t match any of the above mechanisms)';
                    echo '</td>';
                } elseif (strpos($part, '~all') === 0) {
                    echo '<td>';
                    echo '</br>';
                    echo 'Default Policy:';
                    echo '</td><td>';
                    echo '</br>';
                    echo '~all (soft fail: Also allow mails if they are not covered by the mechanisms mentioned above)';
                    echo '</td>';
                } else {
                    echo '<td>';
                    echo '</br>';
                    echo 'Unknown Mechanism:';
                    echo '</td><td>';
                    echo '</br>';
                    echo $part;
                    echo '</td>';
                }
                echo '</tr>';
            }
        }

     }
?>