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
    function dns_check_different($cloudflare_ipv4_addresses, $cloudflare_ipv6_addresses, $cloudflare_txt_records, $cloudflare_cname_records, $cloudflare_mx_records, $cloudflare_ns_records, $google_ipv4_addresses, $google_ipv6_addresses, $google_txt_records, $google_cname_records, $google_mx_records, $google_ns_records, $opendns_ipv4_addresses, $opendns_ipv6_addresses, $opendns_txt_records, $opendns_cname_records, $opendns_mx_records, $opendns_ns_records, $authoritative_ipv4_addresses, $authoritative_ipv6_addresses, $authoritative_txt_records, $authoritative_cname_records, $authoritative_mx_records, $authoritative_ns_records) {
        $ipv4_diff = false;
        $ipv6_diff = false;
        $txt_diff = false;
        $cname_diff = false;
        $mx_diff = false;
        $ns_diff = false;

        if (ipv4_check($cloudflare_ipv4_addresses, $google_ipv4_addresses, $opendns_ipv4_addresses, $authoritative_ipv4_addresses) == false) {
            $ipv4_diff = true;
        }

        if (ipv6_check($cloudflare_ipv6_addresses, $google_ipv6_addresses, $opendns_ipv6_addresses, $authoritative_ipv6_addresses) == false) {
            $ipv6_diff = true;
        }

        if (txt_check($cloudflare_txt_records, $google_txt_records, $opendns_txt_records, $authoritative_txt_records) == false) {
            $txt_diff = true;
        }

        if (cname_check($cloudflare_cname_records, $google_cname_records, $opendns_cname_records, $authoritative_cname_records) == false) {
            $cname_diff = true;
        }

        if (mx_check($cloudflare_mx_records, $google_mx_records, $opendns_mx_records, $authoritative_mx_records) == false) {
            $mx_diff = true;
        }

        if (ns_check($cloudflare_ns_records, $google_ns_records, $opendns_ns_records, $authoritative_ns_records) == false) {
            $ns_diff = true;
        }

        //if any of the checks are true, then $dns_diff too.
        $dns_diff = ($ipv4_diff || $ipv6_diff || $txt_diff || $cname_diff || $mx_diff || $ns_diff);

        return [$dns_diff, $ipv4_diff, $ipv6_diff, $txt_diff, $cname_diff, $mx_diff, $ns_diff];
        
        
    }
?>

<!-- Check if DNS arrays empty
------------------------------------------------------------------

<?php
    function dns_array_empty($cloudflare_ipv4_addresses, $cloudflare_ipv6_addresses, $cloudflare_txt_records, $cloudflare_cname_records, $cloudflare_mx_records, $cloudflare_ns_records, $google_ipv4_addresses, $google_ipv6_addresses, $google_txt_records, $google_cname_records, $google_mx_records, $google_ns_records, $opendns_ipv4_addresses, $opendns_ipv6_addresses, $opendns_txt_records, $opendns_cname_records, $opendns_mx_records, $opendns_ns_records, $authoritative_ipv4_addresses, $authoritative_ipv6_addresses, $authoritative_txt_records, $authoritative_cname_records, $authoritative_mx_records, $authoritative_ns_records) {
        // Check if all IPv4 arrays are empty
        $ipv4_empty = empty($cloudflare_ipv4_addresses) &&
                    empty($google_ipv4_addresses) &&
                    empty($opendns_ipv4_addresses) &&
                    empty($authoritative_ipv4_addresses);

        // Check if all IPv6 arrays are empty
        $ipv6_empty = empty($cloudflare_ipv6_addresses) &&
                    empty($google_ipv6_addresses) &&
                    empty($opendns_ipv6_addresses) &&
                    empty($authoritative_ipv6_addresses);

        // Check if all TXT arrays are empty       
        $txt_empty = empty($cloudflare_txt_records) && 
                    empty($google_txt_records) && 
                    empty($opendns_txt_records) && 
                    empty($authoritative_txt_records);

        // Check if all CNAME arrays are empty
        $cname_empty = empty($cloudflare_cname_records) && 
                    empty($google_cname_records) && 
                    empty($opendns_cname_records) && 
                    empty($authoritative_cname_records);

        // Check if all MX arrays are empty
        $mx_empty = empty($cloudflare_mx_records) && 
                    empty($google_mx_records) && 
                    empty($opendns_mx_records) && 
                    empty($authoritative_mx_records);

        // Check if all NS arrays are empty
        $ns_empty = empty($cloudflare_ns_records) && 
                    empty($google_ns_records) &&    
                    empty($opendns_ns_records) && 
                    empty($authoritative_ns_records);

        //if any of the checks are true, then $dns_empty too.
        $dns_empty = ($ipv4_empty || $ipv6_empty || $txt_empty || $cname_empty || $mx_empty || $ns_empty);

        return [$dns_empty, $ipv4_empty, $ipv6_empty, $txt_empty, $cname_empty, $mx_empty, $ns_empty];

        
    }

?>