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

        if (check_ipv4_same($nameserver_array, $dns_array_result_authoritative) == false) {
            $ipv4_diff = true;
        }

        if (check_ipv6_same($nameserver_array, $dns_array_result_authoritative) == false) {
            $ipv6_diff = true;
        }

        if (check_txt_same($nameserver_array, $dns_array_result_authoritative) == false) {
            $txt_diff = true;
        }

        if (check_cname_same($nameserver_array, $dns_array_result_authoritative) == false) {
            $cname_diff = true;
        }

        if (check_mx_same($nameserver_array, $dns_array_result_authoritative) == false) {
            $mx_diff = true;
        }

        if (check_ns_same($nameserver_array, $dns_array_result_authoritative) == false) {
            $ns_diff = true;
        }

        //if any of the checks are true, then $dns_diff too.
        $dns_diff = ($ipv4_diff || $ipv6_diff || $txt_diff || $cname_diff || $mx_diff || $ns_diff);

        return [$dns_diff, $ipv4_diff, $ipv6_diff, $txt_diff, $cname_diff, $mx_diff, $ns_diff];
        
        
    }

        // Check if all the ipv4 values in the nameserver arrays are the same
        function check_ipv4_same($nameserver_array, $dns_array_result_authoritative) {
            $nameserver_ipv4_values = [];
            $authoritative_ipv4_values = [];
        
            // Extract all the ipv4 values from $nameserver_array
            foreach ($nameserver_array as $nameserver) {
                $nameserver_ipv4_values = array_merge($nameserver_ipv4_values, $nameserver['ipv4']);
            }
        
            // Extract all the ipv4 values from $dns_array_result_authoritative
            foreach ($dns_array_result_authoritative as $entry) {
                $authoritative_ipv4_values = array_merge($authoritative_ipv4_values, $entry['ipv4']);
            }
        
            // Check if all the ipv4 values in $nameserver_array are present in $dns_array_result_authoritative
            $is_ipv4_same = count(array_diff($nameserver_ipv4_values, $authoritative_ipv4_values)) === 0;
        
            // return true or false
            return $is_ipv4_same;
        }
    
        // Check if all the ipv6 values in the nameserver arrays are the same
        function check_ipv6_same($nameserver_array, $dns_array_result_authoritative) {
            $nameserver_ipv6_values = [];
            $authoritative_ipv6_values = [];
        
            // Extract all the ipv6 values from $nameserver_array
            foreach ($nameserver_array as $nameserver) {
                $nameserver_ipv6_values = array_merge($nameserver_ipv6_values, $nameserver['ipv6']);
            }
        
            // Extract all the ipv6 values from $dns_array_result_authoritative
            foreach ($dns_array_result_authoritative as $entry) {
                $authoritative_ipv6_values = array_merge($authoritative_ipv6_values, $entry['ipv6']);
            }
        
            // Check if all the ipv6 values in $nameserver_array are present in $dns_array_result_authoritative
            $is_ipv6_same = count(array_diff($nameserver_ipv6_values, $authoritative_ipv6_values)) === 0;
        
            // return true or false
            return $is_ipv6_same;
        }
    
        // Check if all the txt values in the nameserver arrays are the same
        function check_txt_same($nameserver_array, $dns_array_result_authoritative) {
            $nameserver_txt_values = [];
            $authoritative_txt_values = [];
        
            // Extract all the txt values from $nameserver_array
            foreach ($nameserver_array as $nameserver) {
                $nameserver_txt_values = array_merge($nameserver_txt_values, $nameserver['txt']);
            }
        
            // Extract all the txt values from $dns_array_result_authoritative
            foreach ($dns_array_result_authoritative as $entry) {
                $authoritative_txt_values = array_merge($authoritative_txt_values, $entry['txt']);
            }
        
            // Check if all the txt values in $nameserver_array are present in $dns_array_result_authoritative
            $is_txt_same = count(array_diff($nameserver_txt_values, $authoritative_txt_values)) === 0;
        
            // return true or false
            return $is_txt_same;
        }
    
        // Check if all the cname values in the nameserver arrays are the same
        function check_cname_same($nameserver_array, $dns_array_result_authoritative) {
            $nameserver_cname_values = [];
            $authoritative_cname_values = [];
        
            // Extract all the cname values from $nameserver_array
            foreach ($nameserver_array as $nameserver) {
                $nameserver_cname_values = array_merge($nameserver_cname_values, $nameserver['cname']);
            }
        
            // Extract all the cname values from $dns_array_result_authoritative
            foreach ($dns_array_result_authoritative as $entry) {
                $authoritative_cname_values = array_merge($authoritative_cname_values, $entry['cname']);
            }
        
            // Check if all the cname values in $nameserver_array are present in $dns_array_result_authoritative
            $is_cname_same = count(array_diff($nameserver_cname_values, $authoritative_cname_values)) === 0;
        
            // return true or false
            return $is_cname_same;
        }
    
         // Check if all the mx values in the nameserver arrays are the same
         function check_mx_same($nameserver_array, $dns_array_result_authoritative) {
            $nameserver_mx_values = [];
            $authoritative_mx_values = [];
        
            // Extract all the mx values from $nameserver_array
            foreach ($nameserver_array as $nameserver) {
                $nameserver_mx_values = array_merge($nameserver_mx_values, $nameserver['mx']);
            }
        
            // Extract all the mx values from $dns_array_result_authoritative
            foreach ($dns_array_result_authoritative as $entry) {
                $authoritative_mx_values = array_merge($authoritative_mx_values, $entry['mx']);
            }
        
            // Check if all the mx values in $nameserver_array are present in $dns_array_result_authoritative
            $is_mx_same = count(array_diff($nameserver_mx_values, $authoritative_mx_values)) === 0;
        
            // return true or false
            return $is_mx_same;
        }   
        
        // Check if all the ns values in the nameserver arrays are the same
        function check_ns_same($nameserver_array, $dns_array_result_authoritative) {
            $nameserver_ns_values = [];
            $authoritative_ns_values = [];
    
            // Extract all the ns values from $nameserver_array
            foreach ($nameserver_array as $nameserver) {
                $nameserver_ns_values = array_merge($nameserver_ns_values, $nameserver['ns']);
            }
    
            // Extract all the ns values from $dns_array_result_authoritative
            foreach ($dns_array_result_authoritative as $entry) {
                $authoritative_ns_values = array_merge($authoritative_ns_values, $entry['ns']);
            }
    
            // Check if all the ns values in $nameserver_array are present in $dns_array_result_authoritative
            $is_ns_same = count(array_diff($nameserver_ns_values, $authoritative_ns_values)) === 0;
    
            // return true or false
            return $is_ns_same;
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