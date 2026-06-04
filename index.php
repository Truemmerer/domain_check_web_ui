<?php
// Import other php files
    require_once 'config.php';
    require_once 'src/helper.php';
    require_once 'src/navbar.php';
    require_once 'src/footer.php';
    require_once 'src/startpage.php';
    require_once 'src/result.php';
?>

<html data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Security headers (best-effort) -->
        <?php
            // Send a moderate Content-Security-Policy when headers are still
            // mutable. Allows Bootstrap from jsdelivr (kept compatible with
            // the existing CDN include) and inline styles for Bootstrap-driven
            // theme attributes. No inline scripts.
            if (!headers_sent()) {
                header("X-Content-Type-Options: nosniff");
                header("X-Frame-Options: SAMEORIGIN");
                header("Referrer-Policy: no-referrer");
                header(
                    "Content-Security-Policy: " .
                    "default-src 'self'; " .
                    "img-src 'self' data:; " .
                    "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; " .
                    "script-src 'self' https://cdn.jsdelivr.net; " .
                    "connect-src 'self'; " .
                    "frame-ancestors 'self'; " .
                    "base-uri 'self'; " .
                    "form-action 'self'"
                );
            }
        ?>

        <!-- Bootstrap -->
        <?php
        if($bootstrap_localy) {
            ?>
            <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
            <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
            <?php
        } else {
            ?>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
            <?php
        }

        // OpenSearch
        if ($opensearch_enabled === true) {
            if ($dns_search === true) {
                echo '<link rel="search" type="application/opensearchdescription+xml" title="dns" href="opensearch/dns.xml">';
            }
            if ($whois_search === true) {
                echo '<link rel="search" type="application/opensearchdescription+xml" title="whois" href="opensearch/whois.xml">';
            }
            if ($rdns_search === true) {
                echo '<link rel="search" type="application/opensearchdescription+xml" title="rdns" href="opensearch/rdns.xml">';
            }
            if ($punyconvert_search === true) {
                echo '<link rel="search" type="application/opensearchdescription+xml" title="puny" href="opensearch/punyconvert.xml">';
            }
            if ($spf_search === true) {
                echo '<link rel="search" type="application/opensearchdescription+xml" title="spf" href="opensearch/spf.xml">';
            }
            if ($geolocation === true) {
                echo '<link rel="search" type="application/opensearchdescription+xml" title="spf" href="opensearch/geolocation.xml">';
            }
        }
        // END of OpenSearch

        ?>

        <!-- Own CSS -->
        <link rel="stylesheet" type="text/css" href="src/style/main.css">
        <link rel="stylesheet" type="text/css" href="src/style/desktop.css" media="(min-width: 769px)">

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="assets/window-domain_icon-icons.com_52810.ico">

        <!-- Page Title Can changed in config.php -->
        <title><?php echo h($pageTitle) ?></title>

    </head>
    <body class="body-color">

        <?php
            // --------------------------------------------------------------
            //  Input handling (hardened)
            // --------------------------------------------------------------
            //  - Only GET requests are accepted (matches original behaviour).
            //  - toproof is whitespace-stripped and length-capped.
            //  - action is restricted to a known whitelist.
            //  - cnsa is validated as a hostname OR IP. Invalid -> null.
            // --------------------------------------------------------------

            $allowed_action_keys = ['whois','puny','dnssec','rdns','provider','spf','myip','geo','cdns','dnscheck'];

            if ($_SERVER["REQUEST_METHOD"] === "GET") {
                if (isset($_GET['toproof']) && is_string($_GET['toproof']) && $_GET['toproof'] !== '') {
                    // strip ALL whitespace, normalise (lowercase, drop trailing dot)
                    // and length-cap to a sane domain length.
                    $toproof = preg_replace('/\s+/', '', $_GET['toproof']);
                    if (strlen($toproof) > 320) { // 254 (e-mail) + safety margin
                        $toproof = substr($toproof, 0, 320);
                    }
                    $toproof = normalize_input($toproof);
                } else {
                    $toproof = "";
                }

                if (isset($_GET['action']) && is_string($_GET['action']) && in_array($_GET['action'], $allowed_action_keys, true)) {
                    $action = $_GET['action'];
                } else {
                    $action = "empty";
                }

                if (isset($_GET['cnsa']) && is_string($_GET['cnsa']) && $_GET['cnsa'] !== '') {
                    $cnsa_candidate = preg_replace('/\s+/', '', $_GET['cnsa']);
                    $cnsa_candidate = normalize_input($cnsa_candidate);
                    if (is_safe_host($cnsa_candidate)) {
                        $cnsa = $cnsa_candidate;
                    } else {
                        $cnsa = null;
                    }
                } else {
                    $cnsa = null;
                }
            } else {
                $toproof = "";
                $action = "empty";
                $cnsa = null;
            }

            // Map action to per-feature toggle from config.php
            $feature_enabled = [
                'whois'    => $whois,
                'puny'     => $puny,
                'dnssec'   => $dnssec,
                'rdns'     => $rdns,
                'provider' => $provider,
                'spf'      => $spf,
                'myip'     => $myip,
                'geo'      => $geo,
                'cdns'     => $cdns,
                'dnscheck' => true,
            ];

            $action_allowed = ($action === "empty") || (isset($feature_enabled[$action]) && $feature_enabled[$action] === true);

            /* Case when action and toproof is set */
            if (!empty($toproof) && $action !== "empty" && $action_allowed) {
                echo "<div class='container-fluid'>";
                    show_navbar($toproof, $working_domain, $pageTitle, $whois, $dnssec, $rdns, $puny, $provider, $spf, $myip, $geo, $cdns);
                echo "</div>";
                echo "<div class='container-fluid content'>";
                    show_resultpage($toproof, $action, $cnsa);
                echo "</div>";
                echo "<div class='container-fluid'>";
                    show_footer($impressum, $privacy_policy);
                echo "</div>";

            /* Case when no action or toproof is set */
            } else {
                echo "<div class='container-fluid'>";
                    show_navbar_startpage($working_domain);
                echo "</div>";
                echo "<div class='container-fluid'>";
                    show_startpage($pageTitle, $whois, $dnssec, $rdns, $puny, $provider, $spf, $myip, $geo, $cdns);
                echo "</div>";
                echo "<div class='container-fluid'>";
                    show_footer($impressum, $privacy_policy);
                echo "</div>";
            }

        ?>
    </body>
</html>
