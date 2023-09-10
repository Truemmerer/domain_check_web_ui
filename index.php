<!DOCTYPE html>

    <head>
        <title>Domain Check - WebUI</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
   
        <link rel="stylesheet" type="text/css" href="own_style.css">
        <link rel="icon" type="image/x-icon" href="assets/window-domain_icon-icons.com_52810.ico">

    </head>

    <body class="body-color">

        <?php
        // Add Functions
            require_once 'startpage.php';
            require_once 'navbar.php';
            require_once 'functions/whois.php';
            require_once 'functions/nslookup/nslookup.php';
            require_once 'functions/dnssec.php';
            require_once 'functions/dig.php';
            require_once 'functions/rdns-ptr.php';
            require_once 'functions/healthcheck.php';
            require_once 'functions/helper.php';
            require_once 'config.php';
            require_once 'footer.php';

            
        // END Add Funcitons


        if(isset($_GET['toproof']) && !empty($_GET['toproof'])) {
                $toproof = str_replace(' ', '', $_GET['toproof']);
        } else {
                $toproof = "";
        }

        // Generate navbar
        show_navbar($toproof, $working_domain);

        //What is to do, if a button are clickt

        if ($_SERVER["REQUEST_METHOD"] == "GET") {
             
            // If tooproof is empty
            if(isset($_GET['action']) && !empty($_GET['action'])) : ?>

                <?php if (!$toproof) {
                    ?>
                    <div class="alert alert-info">
                        <strong>Note!</strong> You must enter a domain or IP.</a>.
                    </div>
                    <?php
                    show_startpage();
                } else { ?>

                    <!-- If to proof not empty --> 
                
                    <?php if(isset($_GET['action']) && !empty($_GET['action']) && ($_GET["action"] == "health-check")) : ?>
                        <div class="container-fluid">
                            <?php
                                healthckeck($toproof);
                            ?>
                        </div>
                    <?php endif; ?>    
                
                
                
                    <?php if(isset($_GET['action']) && !empty($_GET['action']) && ($_GET["action"] == "nslookup")) : ?>
                        <div class="container-fluid">
                            <?php
                                build_nslookup($toproof);
                            ?>
                        </div>
                        
                    <?php endif; ?>    
                        
                    
                    <!-- press whois? -->
                    <?php 
                        if(isset($_GET['action']) && !empty($_GET['action']) && ($_GET["action"] == "whois")) : ?>                
                            <div class="container-fluid">                        
                            <?php 
                                whois_output($toproof);
                            ?>
                            </div>
                    
                    <?php endif; ?>  

                    <!-- press dnssec? -->
                    <?php 
                        if(isset($_GET['action']) && !empty($_GET['action']) && ($_GET["action"] == "dnssec")) : ?>
                            <div class="container-fluid">                        
                            <?php 
                                dnssec($toproof);
                            ?>
                            </div>    
                    <?php endif; ?>   

                    <!-- press dig? -->
                    <?php 
                        if(isset($_GET['action']) && !empty($_GET['action']) && ($_GET["action"] == "dig")) : ?>
                            <div class="container-fluid">                        
                            <?php 
                                dig($toproof);
                            ?>
                            </div>    
                    <?php endif; ?>   
                    <!-- press dig? -->
                    <?php 
                        if(isset($_GET['action']) && !empty($_GET['action']) && ($_GET["action"] == "rdns-ptr")) : ?>
                            <div class="container-fluid">                        
                            <?php 
                                rdns_ptr_print($toproof);
                            ?>
                            </div>    
                    <?php endif; ?>   
                <?php } ?>
            <?php endif; ?>    



        <!-- END Press Button -->
        <?php 

        }
        
    
        // Show Startpage, if no action are used
        if (!isset($_GET['action']) || empty($_GET['action'])) {
            show_startpage();
        }
        
        // Shows imprint and privacy policy

        footer($impressum, $privacy_policy, $contribute)
        
        ?>
        


    </body>

</html> 
