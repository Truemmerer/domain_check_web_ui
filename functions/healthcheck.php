<?php

function healthckeck($toproof){

    $whatisit = whatisit($toproof);
    if ($whatisit === 2) {
        // Get tld
        $tld = check_tld($toproof);

        // Get whois
        $whois = whois_check($toproof);

        // DOMAIN STATUS CHECK
        $status = whois_status($whois, $tld);

        // Return Nameserver
        $nameserver = whois_nameserver($whois, $tld);

    } else {
        $status = NULL;
        $nameserver = NULL; 
    }

    // RDNS / PTR CHECK
    list($ip, $rDNS, $ptr) = ptr_rdns_check_ipv4($toproof);



    // Get DNS
    list($cloudflare_ipv4_addresses, $cloudflare_ipv6_addresses, $cloudflare_txt_records, $cloudflare_cname_records, $cloudflare_mx_records, $cloudflare_ns_records, $google_ipv4_addresses, $google_ipv6_addresses, $google_txt_records, $google_cname_records, $google_mx_records, $google_ns_records, $opendns_ipv4_addresses, $opendns_ipv6_addresses, $opendns_txt_records, $opendns_cname_records, $opendns_mx_records, $opendns_ns_records, $authoritative_ipv4_addresses, $authoritative_ipv6_addresses, $authoritative_txt_records, $authoritative_cname_records, $authoritative_mx_records, $authoritative_ns_records) = nslookup($toproof);
    
    // Check if the DNS are different each other
    list($dns_diff, $ipv4_diff, $ipv6_diff, $txt_diff, $cname_diff, $mx_diff, $ns_diff) = dns_check_different($cloudflare_ipv4_addresses, $cloudflare_ipv6_addresses, $cloudflare_txt_records, $cloudflare_cname_records, $cloudflare_mx_records, $cloudflare_ns_records, $google_ipv4_addresses, $google_ipv6_addresses, $google_txt_records, $google_cname_records, $google_mx_records, $google_ns_records, $opendns_ipv4_addresses, $opendns_ipv6_addresses, $opendns_txt_records, $opendns_cname_records, $opendns_mx_records, $opendns_ns_records, $authoritative_ipv4_addresses, $authoritative_ipv6_addresses, $authoritative_txt_records, $authoritative_cname_records, $authoritative_mx_records, $authoritative_ns_records);
    list($dns_empty, $ipv4_empty, $ipv6_empty, $txt_empty, $cname_empty, $mx_empty, $ns_empty) = dns_array_empty($cloudflare_ipv4_addresses, $cloudflare_ipv6_addresses, $cloudflare_txt_records, $cloudflare_cname_records, $cloudflare_mx_records, $cloudflare_ns_records, $google_ipv4_addresses, $google_ipv6_addresses, $google_txt_records, $google_cname_records, $google_mx_records, $google_ns_records, $opendns_ipv4_addresses, $opendns_ipv6_addresses, $opendns_txt_records, $opendns_cname_records, $opendns_mx_records, $opendns_ns_records, $authoritative_ipv4_addresses, $authoritative_ipv6_addresses, $authoritative_txt_records, $authoritative_cname_records, $authoritative_mx_records, $authoritative_ns_records);


    //---------------------------------------------------------------------
    // Open Print Function
    print_healthcheck($status, $nameserver, $rDNS, $ip, $ptr, $ipv4_diff, $ipv6_diff, $txt_diff, $cname_diff, $mx_diff, $ns_diff, $dns_empty, $ipv4_empty, $ipv6_empty, $txt_empty, $cname_empty, $mx_empty, $ns_empty);


}


// Print Healthcheck on the screen
function print_healthcheck($status, $nameserver, $rDNS, $ip, $ptr, $ipv4_diff, $ipv6_diff, $txt_diff, $cname_diff, $mx_diff, $ns_diff, $dns_empty, $ipv4_empty, $ipv6_empty, $txt_empty, $cname_empty, $mx_empty, $ns_empty) {

    // Domain Status
    if ($status === NULL) {
        ?>
        <h3>Domainstatus</h3>
        <h4><span class="badge bg-secondary">The status of an IP cannot be checked</span></h4>     
        <?php
    } else {
        ?>
            <h3>Domainstatus</h3>
            <h4><span class="badge bg-primary"><?php echo $status ?></span></h4>     
        <?php
    }

    // Nameserver List
    if ($nameserver === "Not found") {
        ?>
            <h3>Nameserver</h3>
            <h4><span class="badge bg-danger"><?php echo $nameserver ?></span></h4>
        <?php
    } elseif ($nameserver === NULL) {
        ?>
            <h3>Nameserver</h3>
            <h4><span class="badge bg-secondary">There are no name servers for IPs</span></h4>
        <?php
    } else {    
    ?>
        <h3>Nameserver</h3>
        <h4><span class="badge bg-primary"><?php echo $nameserver ?></span></h4>
    <?php
    }

    // check Same DNS all nameservers
    ?>
        <h3> DNS Check </h3>
        <h4>
        <?php
            // Check if IPv4 same 
            if ($ipv4_diff === true) {
                ?>
                    <span class="badge bg-warning">IPv4</span>   
                <?php
            } elseif ($ipv4_empty === true) {
                ?>
                    <span class="badge bg-danger">IPv4</span>   
                <?php
            } else {
                ?>
                    <span class="badge bg-success">IPv4</span>   
                <?php
            }
            // Check if IPv6 same
            if ($ipv6_diff === true) {
                ?>
                    <span class="badge bg-warning">IPv6</span>   
                <?php
            } elseif ($ipv6_empty === true) {
                    ?>
                    <span class="badge bg-danger">IPv6</span>   
                <?php
            } else {
                ?>
                    <span class="badge bg-success">IPv6</span>   
                <?php
            }
            // Check if txt same 
            if ($txt_diff === true) {
                ?>
                    <span class="badge bg-warning">TXT</span> 
                <?php
            } elseif ($txt_empty === true) {
                ?>
                <span class="badge bg-danger">TXT</span>   
            <?php
            } else {
                ?>
                    <span class="badge bg-success">TXT</span>
                <?php
            }
            // Check if cname same 
            if ($cname_diff === true) {
                ?>
                    <span class="badge bg-warning">CNAME</span> 
                <?php
            } elseif ($cname_empty === true) {
                ?>
                    <span class="badge bg-danger">CNAME</span>   
                <?php
            } else {
                ?>
                    <span class="badge bg-success">CNAME</span>  
                <?php
            }
            // Check if mx same
            if ($mx_diff === true) {
                ?>
                    <span class="badge bg-warning">MX</span> 
                <?php
            } elseif ($mx_empty === true) {
                ?>
                    <span class="badge bg-danger">MX</span>   
                <?php
            } else {
                ?>
                    <span class="badge bg-success">MX</span>
                <?php
            }
            // Check if ns same
            if ($ns_diff === true) {
                ?>
                    <span class="badge bg-warning">NS</span> 
                <?php
            } elseif ($ns_empty === true) {
                ?>
                    <span class="badge bg-danger">NS</span>   
                <?php
            } else {
                ?>
                    <span class="badge bg-success">NS</span> 
                <?php
            }
        ?>
        </h4>

    <?php
    // rDNS 
    if ($rDNS === NULL) { 
        ?>
            <h3>rDNS (IPv4)</h3> 
            <h4><span class="badge bg-danger">No rDNS found</span></h4>
        <?php
    } else {
        ?>
            <h3>rDNS (IPv4)</h3> 
            <h4><span class="badge bg-success"><?php echo $ip?> -> <?php echo $rDNS ?></span></h4>
        <?php
    }

    // PTR
    if ($ptr === true) {
        ?> 
            <h3>PTR (IPv4)</h3> 
            <h4><span class="badge bg-success"><?php echo $ip?> <-> <?php echo $rDNS ?></span></h4>

        <?php  
    } elseif ($ptr === false) {
        ?> 
            <h3>PTR (IPv4)</h3> 
            <h4><span class="badge bg-warning">No PTR set</span></h4>
        <?php  
    }

}

?>


