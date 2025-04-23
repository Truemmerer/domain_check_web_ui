<?php        
        
    function show_navbar($toproof, $working_domain, $pageTitle, $whois, $dnssec, $rdns, $puny, $provider, $spf) {        
        ?>
        <!-- NAV BAR START -->
            <nav class="navbar navbar-expand-lg nav-color navbar-dark">
                <div class="container-fluid">
                    <a class="navbar-brand" href="<?php echo $working_domain ?>">
                       <img src="assets/window-domain_icon-icons.com_52810.png" alt="" style="width:40px;" class="rounded-pill">  <?php echo $pageTitle ?>
                    </a>
                    <br/>
                    <form class="d-flex flex-wrap justify-content-center" action="index.php" method="get">
                        <input type="text" class="form-control me-2 search-color" placeholder="Enter Domain or IP" name="toproof" value="<?php echo $toproof; ?>">
                        <div class="btn-group mt-2 w-100">
                            <button type="submit" class="btn btn-success" style="width: 60%;" name="action" value="dnscheck">DNS Check</button>
                            <button type="button" class="btn btn-dark dropdown-toggle" style="width: 40%;" data-bs-toggle="dropdown" aria-expanded="false">
                            Other tools
                            </button>
                            <?php navbar_menu($whois, $dnssec, $rdns, $puny, $provider, $spf); ?>
                        </div>
                    </form>
                </div>
            </nav>
        <!-- NAV BAR END --> 
    <?php
    }

    function show_navbar_startpage($working_domain) {        
        ?>
        <!-- NAV BAR START -->
            <nav class="navbar navbar-expand-lg nav-color navbar-dark">
                <div class="container-fluid">
                    <a class="navbar-brand" href="<?php echo $working_domain ?>"></a>
                </div>
            </nav>
        <!-- NAV BAR END --> 
    <?php
    }

    function navbar_menu($whois, $dnssec, $rdns, $puny, $provider, $spf) {
        echo '<ul class="dropdown-menu" style="width: 100%;" aria-labelledby="navbarContent">';

                if ($whois) {
                    echo '<li><button type="submit" class="dropdown-item" name="action" value="whois">whois</button></li>';
                }
                if ($dnssec) {
                    echo '<li><button type="submit" class="dropdown-item" name="action" value="dnssec">dnssec</button></li>';
                }
                if ($rdns) {
                    echo '<li><button type="submit" class="dropdown-item" name="action" value="rdns">rDNS & PTR</button></li>';
                }
                if ($puny) {
                    echo '<li><button type="submit" class="dropdown-item" name="action" value="puny">Punyconvert</button></li>';
                }
                if ($provider) {
                    echo '<li><button type="submit" class="dropdown-item" name="action" value="provider">Provider Check</button></li>';
                }
                if ($spf) {
                    echo '<li><button type="submit" class="dropdown-item" name="action" value="spf">SPF Check</button></li>';
                }
            
        echo '</ul>';
    }

?>