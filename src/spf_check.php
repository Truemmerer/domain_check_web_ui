<?php

    /** 
     * Sender Policy Framework (SPF) check
     * @param $toproof - Domain or E-Mail-Adress
     */

     function spf_check($toproof) {

        // v=spf1 mx a include:_spf.webhosting.systems ~all

        // Get TXT Records from Domain
        $command = "dig +short TXT " . escapeshellarg($toproof) . " | sort -u";
        $output = shell_exec($command);
        $txt_records = explode("\n", $output);
        $spf_records = [];

        foreach ($txt_records as $txt_record) {
            echo $txt_record;

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
                    echo 'Mechanism:';
                    echo '</td><td>';
                    echo 'a (Allow mail from the domain\'s A record)';
                    echo '</td>';
                } elseif (strpos($part, 'mx') === 0) {
                    echo '<td>';
                    echo 'Mechanism:';
                    echo '</td><td>';
                    echo 'mx (Allow mail from the domain\'s MX record)';
                    echo '</td>';
                } elseif (strpos($part, 'ip4:') === 0) {
                    echo '<td>';
                    echo 'Mechanism:';
                    echo '</td><td>';
                    echo 'ip4 (Allow mail from the specified IPv4 address)';
                    echo '</td><tr><td></td><td>';
                    echo substr($part, 4);
                    echo '</td>';
                } elseif (strpos($part, 'include:') === 0) {
                    echo '<td>';
                    echo 'Mechanism:';
                    echo '</td><td>';
                    echo '(Include the SPF record from the specified domain)';
                    echo '</td><tr><td></td><td>';
                    echo substr($part, 8);
                    echo '</td>';
                } elseif (strpos($part, '-all') === 0) {
                    echo '<td>';
                    echo 'Default Policy:';
                    echo '</td><td>';
                    echo '-all (Reject all mail that doesn\'t match any of the above mechanisms)';
                    echo '</td>';
                } elseif (strpos($part, '~all') === 0) {
                    echo '<td>';
                    echo 'Default Policy:';
                    echo '</td><td>';
                    echo '~all (Allow all mail that doesn\'t match any of the above mechanisms)';
                    echo '</td>';
                } else {
                    echo '<td>';
                    echo 'Unknown Mechanism:';
                    echo '</td><td>';
                    echo $part;
                    echo '</td>';
                }
                echo '</tr>';
            }
        }

     }
?>