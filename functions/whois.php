<?php

function whois_output($toproof) {

    // Check if $toproof is a domain, ip, url, e-mail-adress
    $whatisthat = whatisit($toproof);

    $whois = whois_check($toproof);

    if ($whatisthat === 2) {
        $tld = check_tld($toproof);
        $status = whois_status($whois, $tld);
        $nameserver = whois_nameserver($whois, $tld);

    } else {
        $tld = "none";
        $status = "Not found";
        $nameserver = "Not found";
    }

    $updated = whois_updatet($whois, $tld);


    build_whois($whois, $status, $nameserver, $updated);
}

function check_tld($toproof) {
    $domain_parts = explode(".", $toproof);
    $tld = end($domain_parts);

    return $tld;
}

function whois_check($toproof) {
    $domain = escapeshellarg($toproof);
    $command = "whois -q " . escapeshellcmd($domain);
    $whois = shell_exec($command);
    $lines = explode("\n", $whois);
    array_shift($lines);
    $whois = implode("\n", $lines);

    return $whois;
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


function build_whois($whois, $status, $nameserver, $updated) {
?>
  
    <!-- Whois Quick -->
    <div class="card card-box-style">
        <div class="card-header" role="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
        <a class="btn" data-bs-toggle="collapse" href="#collapseOne">
            Quick information
        </a>
        </div>
        <div id="collapseOne" class="collapse show" data-bs-parent="#accordion">
            <div class="card-body card-body-style">
                <strong>Status:</strong><br/> <?php echo $status ?>
                <br/><br/>
                <strong>Nameserver:</strong><br/> <?php echo $nameserver ?>
                <br/><br/>
                <strong>Updated:</strong><br/> <?php echo $updated ?>
            </div>
        </div>
    </div>

    <!-- Whois --> 
    <div class="card card-box-style">
        <div class="card-header" role="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
        <a class="btn" data-bs-toggle="collapse" href="#collapseTwo">
            Complete Whois
        </a>
        </div>
        <div id="collapseTwo" class="collapse show" data-bs-parent="#accordion">
            <div class="card-body card-body-style">
                <?php echo nl2br(htmlspecialchars($whois)); ?>
            </div>
        </div>
    </div>

<?php
}
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
?>