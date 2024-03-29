<?php

function healthckeck($toproof){

    $whatisit = whatisit($toproof);
    if ($whatisit === 2) {

        // Check if Domain is in IDN and Convert it to Puny
        if ( is_idn($toproof) ) {
            $toproof = idn_to_puny($toproof);
        }

        // Get tld
        $tld = check_tld($toproof);

        // Get whois
        $whois = whois_check($toproof);

        // DOMAIN STATUS CHECK
        $status = whois_status($whois, $tld);

        // Return Nameserver
        $nameserver = whois_nameserver($whois, $tld);

        // Get DNS
        $nameserver_array = nameserver_check($toproof);            
        $dns_array_result_authoritative = authoritative_check($toproof);    
        
        // Check if the DNS are different each other
        list($dns_diff, $ipv4_diff, $ipv6_diff, $txt_diff, $cname_diff, $mx_diff, $ns_diff) = dns_check_different($nameserver_array, $dns_array_result_authoritative);
        list($dns_empty, $ipv4_empty, $ipv6_empty, $txt_empty, $cname_empty, $mx_empty, $ns_empty) = dns_array_empty($nameserver_array, $dns_array_result_authoritative);

    } else {
        $status = NULL;
        $nameserver = NULL; 
        $dns_diff = $ipv4_diff = $ipv6_diff = $txt_diff = $cname_diff = $mx_diff = $ns_diff = NULL;
        $dns_empty = $ipv4_empty = $ipv6_empty = $txt_empty = $cname_empty = $mx_empty = $ns_empty = NULL;
    }

    // RDNS / PTR CHECK
    list($ip, $rDNS, $ptr) = ptr_rdns_check_ipv4($toproof);


    //dnssec check
    $dnssec_active = dnssec_check_enabled($toproof);
    $dnssec_validate = validate_dnssec($toproof);


    //---------------------------------------------------------------------
    // Open Print Function
    print_healthcheck($dnssec_active, $dnssec_validate, $status, $nameserver, $rDNS, $ip, $ptr, $ipv4_diff, $ipv6_diff, $txt_diff, $cname_diff, $mx_diff, $ns_diff, $dns_empty, $ipv4_empty, $ipv6_empty, $txt_empty, $cname_empty, $mx_empty, $ns_empty, $whatisit);


}


// Print Healthcheck on the screen
function print_healthcheck($dnssec_active, $dnssec_validate, $status, $nameserver, $rDNS, $ip, $ptr, $ipv4_diff, $ipv6_diff, $txt_diff, $cname_diff, $mx_diff, $ns_diff, $dns_empty, $ipv4_empty, $ipv6_empty, $txt_empty, $cname_empty, $mx_empty, $ns_empty, $whatisit) {

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

        if ( $whatisit === 2) {
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
        } else {
            ?>
            <h4><span class="badge bg-secondary">There are no DNS for IPs</span></h4>
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

    ?>

    <?php
    // DNSSEC
    echo '<h3>DNSSEC</h3>'; 

    ?>
    <div class="alert alert-danger">
        <strong>Attention!</strong> This function is still under development and has not yet been sufficiently tested. It is recommended to check the results with another tool. For example: <a href="https://dnssec-analyzer.verisignlabs.com/" class="alert-link">DNSSEC-Analyzer from Verisignlabs.com</a>.
    </div>

    <?php

    if ($dnssec_active === true) { 
        ?>
            <h4><span class="badge bg-success">DNSSEC enabled</span></h4>
        <?php
    } else {
        ?>
            <h4><span class="badge bg-warning">DNSSEC disabled</span></h4>
        <?php
    }
    if ($dnssec_validate === true) { 
        ?>
            <h4><span class="badge bg-success">DNSSEC validated</span></h4>
        <?php
    } else {
        ?>
            <h4><span class="badge bg-warning">DNSSEC not validated</span></h4>
        <?php
    }


}

?>
