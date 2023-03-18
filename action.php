<html>

    <head>
        <title>Domain Check - WebUI</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
   
        <link rel="stylesheet" type="text/css" href="own_style.css">
        <link rel="icon" type="image/x-icon" href="assets/window-domain_icon-icons.com_52810.ico">

        <script>
            function includeHTML() {
              var z, i, elmnt, file, xhttp;
              /* Loop through a collection of all HTML elements: */
              z = document.getElementsByTagName("*");
              for (i = 0; i < z.length; i++) {
                elmnt = z[i];
                /*search for elements with a certain atrribute:*/
                file = elmnt.getAttribute("own-include-html");
                if (file) {
                  /* Make an HTTP request using the attribute value as the file name: */
                  xhttp = new XMLHttpRequest();
                  xhttp.onreadystatechange = function() {
                    if (this.readyState == 4) {
                      if (this.status == 200) {elmnt.innerHTML = this.responseText;}
                      if (this.status == 404) {elmnt.innerHTML = "Page not found.";}
                      /* Remove the attribute, and call this function once more: */
                      elmnt.removeAttribute("own-include-html");
                      includeHTML();
                    }
                  }
                  xhttp.open("GET", file, true);
                  xhttp.send();
                  /* Exit the function: */
                  return;
                }
              }
            }
        </script> 

    </head>

    <body class="body-color">

        <!-- Add Funcitons -->
        <?php
            require_once 'functions/whois.php';
            require_once 'functions/nslookup.php';


        ?>
        <!-- END Add Funcitons -->

        <?php 

            $error = 0;

            if ($_SERVER["REQUEST_METHOD"] == "GET") {
                if(isset($_GET['toproof']) && !empty($_GET['toproof'])){
                    $error = 0;
                
                } else {
                    $error = 1;

                }
            }
        ?>

      <!--  <div own-include-html="navbar.php"></div>        -->

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

        <script>
            includeHTML();
        </script> 



        <!-- CHECK IF ENTER TEXT -->
        <?php if ($error == 1) : ?>
            <div class="alert alert-info">
                <strong>Note!</strong> You must enter a domain or IP.</a>.
            </div>
            <div own-include-html="startpage.html"></div>    
            <script>
                includeHTML();
            </script>     
        <?php endif; ?>
        <!-- END CHECK IF ENTER TEXT -->



        <!-- Press Button -->
        <?php if ($error == 0) : ?>

            <!-- Press nslookup? -->
            <?php 
                if(isset($_GET['action']) && !empty($_GET['action']) && ($_GET["action"] == "nslookup")) : ?>
                <div class="container-fluid">
                <?php
                    nslookup($toproof)
                ?>
                </div>
            
            <?php endif; ?>    
             
         
        <!-- press whois? -->
             <?php 
                if(isset($_GET['action']) && !empty($_GET['action']) && ($_GET["action"] == "whois")) : ?>                
                    <div class="container-fluid">                        
                    <?php 
                        whois_output($toproof) 
                    ?>
                    </div>
            
              <?php endif; ?>  

        <!-- press dnssec? -->
            <?php 
                if(isset($_GET['action']) && !empty($_GET['action']) && ($_GET["action"] == "dnssec")) : ?>
                <div class="container-fluid">
                <iframe src="https://dnssec-analyzer.verisignlabs.com/<?php echo $_GET["toproof"]; ?>" title="" width="100%" height="3000"></iframe> 
                </div>         
            <?php endif; ?>   


        <!-- END Press Button -->
        <?php endif; ?>


    </body>

</html> 
