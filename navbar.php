<?php        
        
    function show_navbar($toproof, $working_domain, $impressum, $privacy_policy) {
?>
        <!-- NAV BAR START -->
        <nav class="navbar navbar-expand-sm nav-color navbar-dark">
            <div class="container-fluid">
                <!-- LOGO START -->
                <a class="navbar-brand" href="<?php echo $working_domain ?>">
                    <img src="assets/window-domain_icon-icons.com_52810.png" alt="" style="width:40px;" class="rounded-pill">  Domain Check - WebUI 
                </a>
                <!-- LOGO END -->

                <!-- FORM START -->
                <div class="container-fluid mt-">
                    <form method="get" action="index.php">
                        <div class="row">
                            <div class="col-3">
                                <input type="text" class="form-control" placeholder="Enter Domain or IP" name="toproof" value="<?php echo $toproof; ?>">
                            </div>
                            <div class="col-2">
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-primary" name="action" value="nslookup">nslookup</button>
                                    <button type="submit" class="btn btn-primary" name="action" value="dig">dig</button>
                                    <button type="submit" class="btn btn-primary" name="action" value="whois">whois</button>
                                    <button type="submit" class="btn btn-primary" name="action" value="dnssec">dnssec</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>         
                <!-- FORM END-->

                <div class="mt-">
                    <div class="row">
                        <div class="col-2">
                            <div class="btn-group">
                                <a href="<?php echo $impressum ?>" class="btn btn-secondary">Legal notice</a>
                                <a href="<?php echo $privacy_policy ?>" class="btn btn-secondary">Privacy policy</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </nav>
        <!-- NAV BAR END --> 
<?php
    }
?>
