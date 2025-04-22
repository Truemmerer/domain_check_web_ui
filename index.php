
<?php
// Import other php files
    require_once 'config.php';
    require_once 'src/navbar.php';
    require_once 'src/footer.php';
    require_once 'src/startpage.php';
    require_once 'src/result.php';
?>

<html data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
        }
        // END of OpenSearch

        ?>

        <!-- Own CSS -->
        <link rel="stylesheet" type="text/css" href="src/style/main.css">
        <link rel="stylesheet" type="text/css" href="src/style/desktop.css" media="(min-width: 769px)">

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="assets/window-domain_icon-icons.com_52810.ico">

        <!-- Page Title Can changed in config.php -->
        <title><?php echo $pageTitle ?></title>

    </head>
    <body class="body-color">
        
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "GET") {
                if(isset($_GET['toproof']) && !empty($_GET['toproof'])) {
                    $toproof = str_replace(' ', '', $_GET['toproof']);
                } else {
                    $toproof = "";
                }
            
                if(!isset($_GET['action']) || empty($_GET['action'])) {
                    $action = "empty";
                } else {
                    $action = $_GET['action'];
                }
            } else {
                $toproof = "";
                $action = "empty";
            }


            $allowed_actions = array_filter(array(
                'whois' => $whois,
                'puny' => $puny,
                'dnssec' => $dnssec,
                'rdns' => $rdns,
                'provider' => $provider,
                'spfcheck' => $spfcheck,
                'dnscheck' => true
            ));
            
            if (isset($_GET['action']) && in_array($_GET['action'], array_keys($allowed_actions)) || $action == "empty") {
                $action_allowed = true;
            } else {
                $action_allowed = false;
            }


            /* Case when action and toproof is set */
            if (!empty($toproof) && $action != "empty" && $action_allowed) {
            echo "<div class='container-fluid'>";
                show_navbar($toproof, $working_domain, $pageTitle, $whois, $dnssec, $rdns, $puny, $provider, $spfcheck);
            echo "</div>";
            echo "<div class='container-fluid content'>";
                show_resultpage($toproof, $action);
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
                    show_startpage($pageTitle, $whois, $dnssec, $rdns, $puny, $provider, $spfcheck);
                echo "</div>";
                echo "<div class='container-fluid'>";
                    show_footer($impressum, $privacy_policy);
                echo "</div>";
            }

        ?>
    </body>
</html>