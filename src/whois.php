<?php

require_once 'src/helper.php';
require_once 'src/punyconvert.php';

function whois_output($toproof) {

    // Check if $toproof is a domain, ip, url, e-mail-adress
    $type = whatisit($toproof);

    if ($type === "none") {
        echo '<div class="alert alert-warning"><strong>Note!</strong> Invalid input.</div>';
        return;
    }

    // Check if Domain is in IDN and Convert it to Puny (ascii) before
    // anything else, so the shell only ever sees ascii.
    if ($type === 'Domain' && is_idn($toproof)) {
        $converted = idn_to_puny($toproof);
        if (is_string($converted) && is_safe_domain($converted)) {
            $toproof = $converted;
        } else {
            echo '<div class="alert alert-warning"><strong>Note!</strong> IDN conversion failed.</div>';
            return;
        }
    }

    // Final safety net before we pass anything to whois
    if ($type === 'Domain' && !is_safe_domain($toproof)) {
        echo '<div class="alert alert-warning"><strong>Note!</strong> Invalid domain.</div>';
        return;
    }
    if ($type === 'IP' && !is_safe_ip($toproof)) {
        echo '<div class="alert alert-warning"><strong>Note!</strong> Invalid IP address.</div>';
        return;
    }
    if ($type === 'E-Mail-Adress' && !is_safe_email($toproof)) {
        echo '<div class="alert alert-warning"><strong>Note!</strong> Invalid e-mail address.</div>';
        return;
    }

    // Get Whois
    $whois = whois_check($toproof, $type);

    build_whois($whois);
}


function get_tld_Server($toproof, $tld_with_dot) {
    // removes the first character if it is a dot
    $tld = get_string_after_last_dot($tld_with_dot);

    // Hard whitelist: TLDs are short ascii strings.
    if (!preg_match('/^[a-zA-Z0-9\-]{2,63}$/', $tld)) {
        return false;
    }

    $command = 'whois -- ' . escapeshellarg($tld);
    $pattern = '/^whois:\s*(.*)$/im';
    $whois = shell_exec($command);
    if ($whois === null) {
        return false;
    }
    if (preg_match_all($pattern, $whois, $matches)) {
        $server = trim(implode('', $matches[1]));
        // The returned whois server itself MUST be a valid hostname; if
        // not we drop it. This prevents any garbage from IANA's whois
        // response from reaching the next whois call.
        if (is_safe_domain($server)) {
            return $server;
        }
    }
    return false;
}

function whois_check($toproof, $type) {

    $whois_server = false;

    if ($type === 'Domain') {

        $tld = get_tld($toproof);
        if ($tld === "none") {
            return "Could not determine TLD";
        }

        $is_excepted = is_excepted($tld);
        if ($is_excepted !== null) {
            return $is_excepted;
        }

        $domain = get_domain($toproof, $tld);
        if (!is_safe_domain($domain)) {
            return "Invalid domain";
        }
        $toproof = $domain;

        $whois_server = get_tld_Server($domain, $tld);
    } elseif ($type === 'IP') {
        if (!is_safe_ip($toproof)) {
            return "Invalid IP";
        }
    } elseif ($type === 'E-Mail-Adress') {
        if (!is_safe_email($toproof)) {
            return "Invalid e-mail";
        }
    } else {
        return "Unsupported lookup type";
    }

    // CRITICAL: use escapeshellarg AND a '--' end-of-options marker so
    // that a value like "-h evil.example" can never be reinterpreted
    // as a whois option. We also pre-validated the argument above.
    if ($whois_server !== false) {
        $command = 'whois -h ' . escapeshellarg($whois_server)
                 . ' -- '       . escapeshellarg($toproof);
    } else {
        $command = 'whois -- ' . escapeshellarg($toproof);
    }

    $whois = shell_exec($command);
    if ($whois === null) {
        return "Whois lookup failed";
    }
    $lines = explode("\n", $whois);
    array_shift($lines);
    $whois = implode("\n", $lines);

    // Do NOT echo the raw command back to the user — it's not needed and
    // it gives an attacker free verification of the executed command.
    return $whois;
}

function whois_status($whois, $tld) {
    $pattern = pattern_status($tld);
    if ($pattern === null) return "Not found";

    if (preg_match_all($pattern, $whois, $matches)) {
        $status = $matches[1];
        if (is_array($status)) {
            // Escape each entry before joining with <br>
            $status = array_map('h', $status);
            $status = implode("<br/>", $status);
        } else {
            $status = h($status);
        }
        return $status;
    }
    return "Not found";
}

function whois_nameserver($whois, $tld) {
    $pattern = pattern_nameserver($tld);
    if ($pattern === null) return "Not found";

    if (preg_match_all($pattern, $whois, $matches)) {
        $nameserver = $matches[1];
        if (is_array($nameserver)) {
            $nameserver = array_map('h', $nameserver);
            $nameserver = implode("<br/>", $nameserver);
        } else {
            $nameserver = h($nameserver);
        }
        return $nameserver;
    }
    return "Not found";
}

function whois_updatet($whois, $tld) {
    $pattern = pattern_updated($tld);
    if ($pattern === null) return "Not found";

    if (preg_match($pattern, $whois, $matches)) {
        return h(trim($matches[1]));
    }
    return "Not found";
}

function domain_status_warning($status) {
    // (intentionally empty in original)
}


function build_whois($whois) {
    echo '<div class="content-header">Whois</div>';
    // nl2br + htmlspecialchars protects against XSS coming from a hostile
    // whois server that injects HTML into its response.
    echo nl2br(h($whois));
}
?>

<!-- ________________________________________________________________________________________________________________________________________________________________________________________________________________________ -->

<!-- .tld Pattern List -->

<!-- ________________________________________________________________________________________________________________________________________________________________________________________________________________________ -->

<?php

function pattern_status($tld) {
    $tld = get_string_after_last_dot((string)$tld);
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
    $tld = get_string_after_last_dot((string)$tld);
    switch ($tld) {
        case 'com':
        case 'net':
        case 'org':
            return '/^Name Server:\s*(.*)$/im';
        case 'de':
            return '/^Nserver:\s*(.*)$/im';
        case 'eu':
            return '/^Name servers:\s*(.*)$/im';
        case 'at':   // Bugfix: was `case 'at';` (semicolon)
            return '/^nserver:\s*(.*)$/im';
        default:
            return null;
    }
}

function pattern_updated($tld) {
    $tld = get_string_after_last_dot((string)$tld);
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
    }
    return null;
}

function is_excepted($tld) {

    $tld_without_dot = get_string_after_last_dot($tld);

    $yamlFile = __DIR__ . "/excepted_tlds.yaml";
    $except_list = parse_yaml_file_safe($yamlFile);
    if (!is_array($except_list)) {
        return null;
    }

    // Match against the bare tld (without leading dot) — original code
    // checked array_key_exists($tld, ...) but then returned
    // $except_list[$tld_without_dot]. We now consistently use the
    // dotless form and escape the output for safe HTML rendering.
    if (array_key_exists($tld_without_dot, $except_list)) {
        return h((string)$except_list[$tld_without_dot]);
    }
    return null;
}

?>
