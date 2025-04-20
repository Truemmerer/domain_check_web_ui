<?php


function show_startpage($pageTitle, $whois, $dnssec, $rdns, $puny, $provider, $spfcheck) {

    require_once 'navbar.php';
    ?>
    
    <div class="startpage_welcome">
        <div class=container><img src="./assets/window-domain_icon-icons.com_52810.png" width="64"> <?php echo $pageTitle; ?> </div>  
    </div>

    <div class="startpage_search">

        <form class="d-flex flex-wrap justify-content-center" action="index.php" method="get">
            <input type="text" class="form-control startpage-searchbar search-color" placeholder="Enter Domain or IP" name="toproof" value=""; ?>
            <div class="btn-group mt-2 w-100">
                <button type="submit" class="btn btn-success" style="width: 60%;" name="action" value="dnscheck">DNS Check</button>
                <button type="button" class="btn btn-dark dropdown-toggle" style="width: 40%;" data-bs-toggle="dropdown" aria-expanded="false">
                Other tools
                </button>
                <?php navbar_menu($whois, $dnssec, $rdns, $puny, $provider, $spfcheck); ?>
            </div>
        </form>
    </div> 

<?php 
    }
?>