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



    // Check that toproof is not empty
    if(empty(['toproof'])) {
        show_startpage();
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
                echo '<strong>Note!</strong> You have not entered a valid domain</a>';
            echo '</div>';
            return;
        }
    }


    // Open all functions
    
    if ($action === "whois") {
        whois_output($toproof);
    } elseif ($action === "puny") {
        punyconvert_print($toproof);
    } elseif ($action === "dnscheck") {
        dns_check($toproof, $tld);
    } elseif ($action === "spfcheck") {
        spf_check($toproof);
    } elseif ($action === "provider") {
        provider_check($toproof);
    }

}
?>