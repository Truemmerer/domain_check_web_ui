<?php

    function rdns_ptr($toproof) {

        $type = whatisit($toproof);

        // if $toproof not a ip, domain, url or e-mail-adress
        if($type == 0) {
            return;

        // if $toproof a ip    
        } elseif($type == 1) {
            $rdns = gethostbyaddr($toproof);

            // Check if rdns == domain
            if (!$rdns || !filter_var($rdns, FILTER_VALIDATE_DOMAIN)) {
                // $rdns is either empty or not a valid domain

                $ip = $toproof;
                $ptr = false;
                $rdns = NULL;

                return array($rdns, $ip, $ptr);
            }

            // search rdns for ip-Adresses

            $rdns_ip = gethostbynamel($rdns);
            $ptr = NULL;

            foreach ($rdns_ip as $ip) {
                if ($toproof === $ip) {
                    $ptr = true;
                    break;
                }
            }

            // if rdns not link back to $toproof_IP
            if ($ptr === NULL) {
                $ptr = false;
            }


        // if $toproof a domain
        } elseif($type == 2) {

            $ip_array = gethostbynamel($toproof);
            $ptr = NULL;

            foreach ($ip_array as $ip) {
                $rdns = gethostbyaddr($ip);
                if ($rdns === $toproof) {
                    $ptr = true;
                    break;
                }
            }

            if ($ptr == NULL) {
                $ptr = false;
            }

        }

        return array($rdns, $ip, $ptr);
    }



    function rdns_ptr_print($toproof) {

        list($rdns, $ip, $ptr) = rdns_ptr($toproof);

        //--------------------
        // if rDNS not present
        //--------------------
        if ($rdns === NULL) {
            ?>
            <div class="alert alert-warning">
                <strong>rDNS: </strong>An rDNS could not be detected.
            </div>
            <div class="alert alert-warning">
                <strong>PTR: </strong>Since no rDNS is set, there cannot be a PTR.
            </div>
            <?php
            return;
        }

        //--------------------
        // if rDNS is present
        //--------------------
        // if ptr is present
        if ($ptr === true) {
            ?>
            <div class="alert alert-success">
                <strong>rDNS: </strong>The rDNS of the IP <?php echo $ip ?> is <?php echo $rdns ?>
            </div>
            <div class="alert alert-success">
                <strong>PTR: </strong>A PTR is set. The IP <?php echo $ip ?> points to <?php echo $rdns ?> (rDNS), which in turn points to the IP <?php echo $ip ?> (DNS).
            </div>
            <?php
        // if ptr is not present
        } elseif ($ptr === false) {
            ?>
            <div class="alert alert-success">
                <strong>rDNS: </strong>The rDNS of the IP <?php echo $ip ?> is <?php echo $rdns ?>
            </div>
            <div class="alert alert-warning">
                <strong>PTR: </strong>A PTR is not set. The IP <?php echo $ip ?> points to <?php echo $rdns ?> (rDNS), and didn't points to <?php echo $toproof ?> (DNS).
            </div>
            <?php
        }

        ?>

        <!-- rDNS and PTR explained -->
        <div class="card bg-secondary">
                <div class="card-header" role="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                <a class="btn" data-bs-toggle="collapse" href="#collapseOne">
                    rDNS and PTR explained
                </a>
                </div>
                <div id="collapseOne" class="collapse show" data-bs-parent="#accordion">
                    <div class="card-body card-body-style">
                        <img class="img-fluid" src="./assets/PTR.png">
                    </div>
                </div>
            </div>

        <?php

    }

?>

