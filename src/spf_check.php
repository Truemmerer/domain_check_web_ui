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
            echo $txt_record . " SPF1 <br/>";

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

            foreach ($parts as $part) {
                if (strpos($part, 'v=') === 0) {
                    echo 'Version: ' . substr($part, 2) . ' (SPF version)<br>';
                } elseif (strpos($part, 'a') === 0) {
                    echo 'Mechanism: a (Allow mail from the domain\'s A record)<br>';
                } elseif (strpos($part, 'mx') === 0) {
                    echo 'Mechanism: mx (Allow mail from the domain\'s MX record)<br>';
                } elseif (strpos($part, 'ip4:') === 0) {
                    echo 'Mechanism: ip4 (Allow mail from the specified IPv4 address) - ' . substr($part, 4) . '<br>';
                } elseif (strpos($part, 'include:') === 0) {
                    echo 'Mechanism: include (Include the SPF record from the specified domain) - ' . substr($part, 8) . '<br>';
                } elseif (strpos($part, '-all') === 0) {
                    echo 'Default Policy: -all (Reject all mail that doesn\'t match any of the above mechanisms)<br>';
                } elseif (strpos($part, '~all') === 0) {
                    echo 'Default Policy: ~all (Allow all mail that doesn\'t match any of the above mechanisms)<br>';
                } else {
                    echo 'Unknown Mechanism: ' . $part . '<br>';
                }
            }
        }

     }
?>