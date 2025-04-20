<?php

function whois_output($toproof) {

    // Check if $toproof is a domain, ip, url, e-mail-adress
    $type = whatisit($toproof);

    // Check if Domain is in IDN and Convert it to Puny
    if ( is_idn($toproof) ) {
        $toproof = idn_to_puny($toproof);
    }

    // Get Whois
    $whois = whois_check($toproof, $type);

    /*
    if ($whatisthat === "Domain") {

        $tld = get_tld($toproof);
        $status = whois_status($whois, $tld);
        $nameserver = whois_nameserver($whois, $tld);

    } else {
        $tld = "none";
        $status = "Not found";
        $nameserver = "Not found";
    }

    $updated = whois_updatet($whois, $tld);
    */

    build_whois($whois/*, $status, $nameserver, $updated*/);
}


function get_tld_Server($toproof, $tld_with_dot) {
    // removes the first character if it is a dort
    $tld = get_string_after_last_dot($tld_with_dot);
    $comamand = 'whois ' . $tld;
    $pattern = '/^whois:\s*(.*)$/im';
    $whois = shell_exec($comamand);    
    if (preg_match_all($pattern, $whois, $matches)) {
        $server = implode($matches[1]);
        return $server;
    } else {
        return false;
    }
}

function whois_check($toproof, $type) {

    $whois_server = false;

    if ($type === 'Domain') {
        
        // get a domain if $toproof is a $subdoamin
        $tld = get_tld($toproof);
        $domain = get_domain($toproof, $tld);

        // get whois server of tld
        $whois_server = get_tld_Server($domain, $tld);
    } 


    if ($whois_server != false) {
        $command = 'whois -h ' . $whois_server . ' ' . escapeshellcmd($toproof); 
    } else {
        $command = 'whois ' . escapeshellcmd($toproof);
    }
    $whois = shell_exec($command);
    $lines = explode("\n", $whois);
    array_shift($lines);
    $whois = implode("\n", $lines);

    $result = $whois . "\n" . "-------- \n" . "The following command was used:\n" . $command;
    return $result;
}

function whois_status($whois, $tld) {
    $pattern = pattern_status($tld);

    if (preg_match_all($pattern, $whois, $matches)) {
        $status = $matches[1];

        if (is_array($status)) {
            $status = implode("<br/>", $status);
        }

        return $status;
    } else {
        $status = "Not found";
    }
}

function whois_nameserver($whois, $tld) {
    $pattern = pattern_nameserver($tld);
   
    if (preg_match_all($pattern, $whois, $matches)) {
        $nameserver = $matches[1];

        if (is_array($nameserver)) {
            $nameserver = implode("<br/>", $nameserver);
        }
        return $nameserver;
    } else {
        $nameserver = "Not found";
    }
}

function whois_updatet($whois, $tld) {
    $pattern = pattern_updated($tld);

    if (preg_match($pattern, $whois, $matches)) {
        $updated = trim($matches[1]);
        return $updated;
    } else {
        $updated = "Not found";
        return $updated;
    }
}

function domain_status_warning($status) {



}


function build_whois($whois/*, $status, $nameserver, $updated*/) {
    echo '<div class="content-header">Whois</div>';
    echo nl2br(htmlspecialchars($whois));}
?>

<!-- ________________________________________________________________________________________________________________________________________________________________________________________________________________________ -->

<!-- .tld Pattern List

<!-- ________________________________________________________________________________________________________________________________________________________________________________________________________________________ -->

<?php

function pattern_status($tld) {
    switch ($tld) {
        case 'com':
        case 'net':
        case 'org':
            return '/^Domain Status:\s*(.*)$/im';
        case 'de':
        case 'eu':
            return '/^Status:\s*(.*)$/im';
        default:
            return null;
    }
}

function pattern_nameserver($tld) {
    switch ($tld) {
        case 'com':
        case 'net':
        case 'org':
            return '/^Name Server:\s*(.*)$/im';
        case 'de':
            return '/^Nserver:\s*(.*)$/im';
        case 'eu':
            return '/^Name servers:\s*(.*)$/im';
        case 'at';
            return '/^nserver:\s*(.*)$/im';
        default:
            return null;
    }

}

function pattern_updated($tld) {
    switch ($tld) {
        case 'none':
            return '/^last-modified:\s*(.*)$/im';
        case 'com':
        case 'net':
        case 'org':
            return '/^Updated Date:\s*(.*)$/im';
        case 'de':
        case 'eu':
            return '/^Changed:\s*(.*)$/im';
        default:
            return null;
    }

}


function pattern_warn_status($status) {
    if (preg_match('/^clientTransferProhibited/i', $status)) {
        return 'Warning: Domain transfer is prohibited.';
    } elseif (preg_match('/^clientUpdateProhibited/i', $status)) {
        return 'Warning: Domain Update is prohibited.';
    } elseif (preg_match('/^clientDeleteProhibited/i', $status)) {
        return 'Warning: Domain Delete is prohibited.';
    } else {
        return null;
    }
}

?>