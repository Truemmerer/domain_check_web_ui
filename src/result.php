<?php

function show_resultpage($toproof, $action) {

    // Import all files that needed
    require_once 'src/whois.php';
    require_once 'src/startpage.php';
    require_once 'src/helper.php';
    require_once 'src/punyconvert.php';
    require_once 'src/dnscheck.php';
    require_once 'src/spf_check.php';
    require_once 'src/provider_check.php';
    require_once 'config.php';

    // Check that toproof is not empty
    if(empty(['toproof'])) {
        echo '<div class="alert alert-warning">';
        echo '<strong>Note!</strong> Please enter something to be checked</a>';
        echo '</div>';        
    return;
    }

    // Check if user entry is a domain, ip, url, e-mail-adress
    $type = whatisit($toproof);

    // Check if domain is a valid domain
    if ($type === "Domain") {
        $tld = get_tld($toproof);

        // domain is not valid
        if ($tld === "none") {
            echo '<div class="alert alert-warning">';
                echo '<strong>Note!</strong> You have not entered a valid domain or ip</a>';
            echo '</div>';
            return;
        }
    }

    // Filter Functions that are only compatible with Domain
    if ($type !== "Domain" && $action !== "whois") {
        echo '<div class="alert alert-warning">';
        echo '<strong>Note!</strong> The function ' . $action . ' is only supported together with a domain</a>';
        echo '</div>';
        return;
    }

    // Open all functions
    
    if ($action === "whois") {
        whois_output($toproof);    
    } elseif ($action === "puny" && $type === "Domain") {
        punyconvert_print($toproof);
    } elseif ($action === "dnscheck" && $type === "Domain") {
        dns_check($toproof, $tld);
    } elseif ($action === "spfcheck" && $type === "Domain") {
        spf_check($toproof);
    } elseif ($action === "provider" && $type === "Domain") {
        provider_check($toproof);
    }
}
?>