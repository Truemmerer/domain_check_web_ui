<!DOCTYPE html>
<html>

    <body>

        <!-- NAV BAR START -->
        <nav class="navbar navbar-expand-sm nav-color navbar-dark">
            <div class="container-fluid">
                <!-- LOGO START -->
                <a class="navbar-brand" href="https://doch.truemmerer.de/">
                    <img src="assets/window-domain_icon-icons.com_52810.png" alt="" style="width:40px;" class="rounded-pill">  Domain Check - WebUI 
                </a>
                <!-- LOGO END -->

                <!-- FORM START -->
                <div class="container-fluid mt-">
                    <form method="get" action="action.php">
                        <div class="row">
                            <div class="col-3">
                                <input type="text" class="form-control" placeholder="Enter Domain or IP" name="toproof" value="<?php echo $_GET["toproof"]; ?>">
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
                                <button type="submit" class="btn btn-secondary" name="action" value="impressum">Impressum</button>
                                <button type="submit" class="btn btn-secondary" name="action" value="Datenschutzerklärung">Datenschutz</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </nav>
        <!-- NAV BAR END --> 
    </body>
</html>
