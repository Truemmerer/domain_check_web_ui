<?php

function whois_output($toproof) {
    $tld = check_tld($toproof);
    $whois = whois_check($toproof);
    $status = whois_status($whois, $tld);
    $nameserver = whois_nameserver($whois, $tld);
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
        $status = $matches[1][0];
        return $status;
    } else {
        $status = "Not found";
    }
}

function whois_nameserver($whois, $tld) {
    $pattern = pattern_nameserver($tld);
   
    if (preg_match_all($pattern, $whois, $matches)) {
        $nameserver = $matches[1];
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
    <div class="row">
        <!-- Whois --> 
        <div class="col-sm-8">
            <?php echo nl2br(htmlspecialchars($whois)); ?>
        </div>
        <!-- Whois Quick -->
        <div class="col-sm-4">

            <div class="card card-box-style">
                <div class="card-header" role="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                <a class="btn" data-bs-toggle="collapse" href="#collapseOne">
                    Quick information
                </a>
                </div>
                <div id="collapseOne" class="collapse show" data-bs-parent="#accordion">
                <div class="card-body card-body-style">
                    Status: <?php echo $status ?>
                    <br/>
                    Nameserver: <?php echo "" , implode(", ", $nameserver); ?>
                    <br/>
                    Updated: <?php echo $updated ?>
                </div>
                </div>
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
        default:
            return null;
    }

}

function pattern_updated($tld) {
    switch ($tld) {
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