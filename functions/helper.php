<!---
1) Proof - IP, Domain, URL, E-Mail-Adress
2) extract Domain from a Subdomain
3) List the Nameservers
4) Check the tld

<!-- Check what is to proof
------------------------------------------------------------------
<?php

    // 0 = check failed
    // 1 = IP
    // 2 = Domain
    // 3 = URL  -- not used
    // 4 = E-Mail-Adress -- not used

    function whatisit($toproof) {
        if (filter_var($toproof, FILTER_VALIDATE_IP)) {
            return 1;
        } elseif (filter_var($toproof, FILTER_VALIDATE_DOMAIN)) {
            return 2;
        } else {
            ?>
                <div class="alert alert-info">
                    <strong>Note!</strong> You must enter a domain or IP.</a>.
                </div>
            <?php

            return 0;
        }
    }

?>

<!-- Extract Domain from a Subdomain
------------------------------------------------------------------

<?php 
    function extractDomain($subdomain) {

        // Split the domain by periods
        $parts = explode('.', $subdomain);

        // Check if the domain has enough parts
        if (count($parts) > 2) {
            // Extract the last two parts as the main domain
            $mainDomain = $parts[count($parts) - 2] . '.' . $parts[count($parts) - 1];
            $cleanDomain = rtrim($mainDomain, "'");
        return $cleanDomain;
    }

        // Return the original domain if it doesn't have enough parts
        return $subdomain;
        
    }
?>

<!-- Extract the Nameserver of a Domain
------------------------------------------------------------------

<?php 
    function nameserver_list($domain) {

        $nsRecords = dns_get_record($domain, DNS_NS);

        if ($nsRecords === false) {
            return false;
        } else {
            return $nsRecords;
        }
    }
?>

<!-- Get authorative nameserver IP and Name
------------------------------------------------------------------

<?php
    // get nameserver ips
    function get_nameserver_ip($toproof) {
        // get the domain from a subdomain
        $truedomain = extractDomain($toproof);
        // get Nameservers with dig
        $output_ns_detect = shell_exec("dig +short NS $truedomain");
        $nameservers = explode("\n", trim($output_ns_detect));
        $nameserver_array = array();
        foreach ($nameservers as $nameserver) {
            // get IP of a Nameserver
            $ip = trim(shell_exec("dig +short A $nameserver"));
            // add IP to array
            $nameserver_ips[] = $ip;

            $nameserver_array[] = [
                'ns_ip' => $ip,
                'ns_name' => $nameserver,
            ];

        }

        return $nameserver_array;

    }
?>

<!-- Extract the tld
------------------------------------------------------------------
<?php 
    function check_tld($toproof) {
        $domain_parts = explode(".", $toproof);
        $tld = end($domain_parts);

        return $tld;
    }
?>

<!-- Check if DNS differ each Nameserver
------------------------------------------------------------------
<?php 
    function dns_check_different($nameserver_array, $dns_array_result_authoritative) {
              
        $ipv4_diff = false;
        $ipv6_diff = false;
        $txt_diff = false;
        $cname_diff = false;
        $mx_diff = false;
        $ns_diff = false;

        // merge arrays
        $mergedArray = array_merge_recursive($nameserver_array, $dns_array_result_authoritative);

        //print_r($mergedArray);

        if (check_ipv4_same($mergedArray) == false) {
            $ipv4_diff = true;
        }

        if (check_ipv6_same($mergedArray) == false) {
            $ipv6_diff = true;
        }

        if (check_txt_same($mergedArray) == false) {
            $txt_diff = true;
        }

        if (check_cname_same($mergedArray) == false) {
            $cname_diff = true;
        }

        if (check_mx_same($mergedArray) == false) {
            $mx_diff = true;
        }

        if (check_ns_same($mergedArray) == false) {
            $ns_diff = true;
        }

        //if any of the checks are true, then $dns_diff too.
        $dns_diff = ($ipv4_diff || $ipv6_diff || $txt_diff || $cname_diff || $mx_diff || $ns_diff);

        return [$dns_diff, $ipv4_diff, $ipv6_diff, $txt_diff, $cname_diff, $mx_diff, $ns_diff];
        
        
    }

        // Check if all the ipv4 values in the nameserver arrays are the same
        function check_ipv4_same($mergedArray) {
            $entries = array_map(function($entry) {
                return $entry['ipv4'];
            }, $mergedArray);
        
            $firstentry = $entries[0];
        
            foreach ($entries as $entry) {
                if (count($entry) !== count($firstentry)) {
                    return false;
                }
                foreach ($entry as $value) {
                    if (!in_array($value, $firstentry)) {
                        return false;
                    }
                }
            }
        
            return true; // All entries are the same
        }
        
    
        // Check if all the ipv6 values in the nameserver arrays are the same
        function check_ipv6_same($mergedArray) {
            $entries = array_map(function($entry) {
                return $entry['ipv6'];
            }, $mergedArray);
        
            $firstentry = $entries[0];
        
            foreach ($entries as $entry) {
                if (count($entry) !== count($firstentry)) {
                    return false;
                }
                foreach ($entry as $value) {
                    if (!in_array($value, $firstentry)) {
                        return false;
                    }
                }
            }
        
            return true; // All entries are the same
        }
    
        // Check if all the txt values in the nameserver arrays are the same
        function check_txt_same($mergedArray) {
            $entries = array_map(function($entry) {
                return $entry['txt'];
            }, $mergedArray);
        
            $firstentry = $entries[0];
        
            foreach ($entries as $entry) {
                if (count($entry) !== count($firstentry)) {
                    return false;
                }
                foreach ($entry as $value) {
                    if (!in_array($value, $firstentry)) {
                        return false;
                    }
                }
            }
        
            return true; // All entries are the same
        }
        // Check if all the cname values in the nameserver arrays are the same
        function check_cname_same($mergedArray) {
            $entries = array_map(function($entry) {
                return $entry['cname'];
            }, $mergedArray);
        
            $firstentry = $entries[0];
        
            foreach ($entries as $entry) {
                if (count($entry) !== count($firstentry)) {
                    return false;
                }
                foreach ($entry as $value) {
                    if (!in_array($value, $firstentry)) {
                        return false;
                    }
                }
            }
        
            return true; // All entries are the same
        }

         // Check if all the mx values in the nameserver arrays are the same
         function check_mx_same($mergedArray) {
            $mxEntries = array_map(function($entry) {
                return $entry['mx'];
            }, $mergedArray);
        
            $firstentry = $mxEntries[0];
        
            foreach ($mxEntries as $entry) {
                if (count($entry) !== count($firstentry)) {
                    return false;
                }
                foreach ($entry as $mx) {
                    if (!in_array($mx, $firstentry)) {
                        return false;
                    }
                }
            }
        
            return true; // All MX entries are the same
        }

        // Check if all the ns values in the nameserver arrays are the same
        function check_ns_same($mergedArray) {
            $entries = array_map(function($entry) {
                return $entry['ns'];
            }, $mergedArray);
        
            $firstentry = $entries[0];
        
            foreach ($entries as $entry) {
                if (count($entry) !== count($firstentry)) {
                    return false;
                }
                foreach ($entry as $value) {
                    if (!in_array($value, $firstentry)) {
                        return false;
                    }
                }
            }
        
            return true; // All entries are the same
        }
    
?>


<!-- Check if DNS arrays empty
------------------------------------------------------------------

<?php
function dns_array_empty($nameserver_array, $dns_array_result_authoritative) {

    // Check if all IPv4 arrays are empty
    $ipv4_empty = all_array_entries_empty($nameserver_array, 'ipv4') &&
        all_array_entries_empty($dns_array_result_authoritative, 'ipv4');

    // Check if all IPv6 arrays are empty
    $ipv6_empty = all_array_entries_empty($nameserver_array, 'ipv6') &&
        all_array_entries_empty($dns_array_result_authoritative, 'ipv6');

    // Check if all TXT arrays are empty       
    $txt_empty = all_array_entries_empty($nameserver_array, 'txt') &&
        all_array_entries_empty($dns_array_result_authoritative, 'txt');

    // Check if all CNAME arrays are empty
    $cname_empty = all_array_entries_empty($nameserver_array, 'cname') &&
        all_array_entries_empty($dns_array_result_authoritative, 'cname');

    // Check if all MX arrays are empty
    $mx_empty = all_array_entries_empty($nameserver_array, 'mx') &&
        all_array_entries_empty($dns_array_result_authoritative, 'mx');

    // Check if all NS arrays are empty
    $ns_empty = all_array_entries_empty($nameserver_array, 'ns') &&
        all_array_entries_empty($dns_array_result_authoritative, 'ns');

    // If any of the checks are true, then $dns_empty too.
    $dns_empty = ($ipv4_empty || $ipv6_empty || $txt_empty || $cname_empty || $mx_empty || $ns_empty);

    return [$dns_empty, $ipv4_empty, $ipv6_empty, $txt_empty, $cname_empty, $mx_empty, $ns_empty];
}

function all_array_entries_empty($array, $key) {
    foreach ($array as $entry) {
        if (!empty($entry[$key])) {
            return false;
        }
    }
    return true;
}

?>