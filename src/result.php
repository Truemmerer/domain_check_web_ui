<?php

function show_resultpage($toproof, $action, $cnsa) {

    // Import all files that needed
    require_once 'src/helper.php';
    require_once 'src/whois.php';
    require_once 'src/startpage.php';
    require_once 'src/punyconvert.php';
    require_once 'src/dnscheck.php';
    require_once 'src/spf_check.php';
    require_once 'src/provider_check.php';
    require_once 'src/rdns.php';
    require_once 'src/myip.php';
    require_once 'src/geo.php';
    //require_once 'src/dnssec.php';
    require_once 'src/cdns.php';
    require_once 'config.php';

    // Bugfix: previously `empty(['toproof'])` which is always false
    if (!isset($toproof) || trim((string)$toproof) === '') {
        echo '<div class="alert alert-warning">';
        echo '<strong>Note!</strong> Please enter something to be checked';
        echo '</div>';
        return;
    }

    // Check if user entry is a domain, ip, url, e-mail-adress
    $type = whatisit($toproof);

    if ($type === "none") {
        echo '<div class="alert alert-warning">';
        echo '<strong>Note!</strong> You have not entered a valid domain, IP or e-mail address';
        echo '</div>';
        return;
    }

    // Check if domain is a valid domain (TLD known)
    $tld = null;
    if ($type === "Domain") {
        $tld = get_tld($toproof);
        if ($tld === "none") {
            echo '<div class="alert alert-warning">';
            echo '<strong>Note!</strong> You have not entered a valid domain or ip';
            echo '</div>';
            return;
        }
    }

    // Filter Functions that are only compatible with Domain
    if ($type !== "Domain" && $action !== "whois" && $action !== "rdns" && $action !== "geo" && $action !== "myip") {
        echo '<div class="alert alert-warning">';
        echo '<strong>Note!</strong> The function ' . h($action) . ' is only supported together with a domain';
        echo '</div>';
        return;
    }

    // Open all functions
    if ($action === "whois") {
        whois_output($toproof);
    } elseif ($action === "rdns") {
        rdns_ptr($toproof);
    } elseif ($action === "puny" && $type === "Domain") {
        punyconvert_print($toproof);
    } elseif ($action === "dnscheck" && $type === "Domain") {
        dns_check($toproof, $tld);
    } elseif ($action === "spf" && $type === "Domain") {
        spf_check($toproof);
    } elseif ($action === "provider" && $type === "Domain") {
        provider_check($toproof);
    } elseif ($action === "myip") {
        myip();
    } elseif ($action === "geo") {
        geo($toproof, $type);
    } elseif ($action === "cdns" && $type === "Domain") {
        cdns($toproof, $cnsa);
    }
}
?>
