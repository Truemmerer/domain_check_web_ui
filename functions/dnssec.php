<?php

    // load php-composer pear/net_dns2;
    require_once 'vendor/autoload.php';
    use Net_DNS2_Resolver as Resolver;

    function dnssec($toproof) {

        $dnsKey = get_dnskey($toproof);
        $ds = get_ds($toproof);
        $rrsig = get_rrsig($toproof);
        $dnssec_enabled = dnssec_check_enabled($toproof);
        $dnssec_validate = validate_dnssec($toproof);

        print_dnssec($dnsKey, $ds, $rrsig, $dnssec_enabled, $dnssec_validate);


    }
?> 


<?php

    // Check if DNSSEC enabled
    function dnssec_check_enabled($toproof) {
        $resolver = new Net_DNS2_Resolver();
        $resolver->dnssec = true;

        $response = $resolver->query($toproof, 'DNSKEY');
        $dnssecEnabled = $response->header->ad;

        if ($dnssecEnabled) {
            return true;
        } else {
            return false;
        }
    }

    function validate_dnssec($toproof) {
        $resolver = new Resolver();
        $resolver->dnssec = true;

        try {
            $response = $resolver->query($toproof, 'DNSKEY');
            
            if ($response->header->ad) {
                return true;
            } else {
                return false;
            }
        } catch (Net_DNS2_Exception $e) {
            //echo "Error: " . $e->getMessage();
        }
    }

    // -----------------------------------------
    // GET NEEDED DNS
    // -----------------------------------------

    // Get DNSKEY/DNSSECKEY
    function get_dnskey($toproof) {
        $resolver = new Resolver();
        $resolver->dnssec = true;
    
        $dnssecarray = [];
    
        try {
            $response = $resolver->query($toproof, 'DNSKEY');
            
            foreach ($response->answer as $record) {
                if ($record->type === 'DNSKEY') {
                    if (is_string($record->rdata)) {
                        $dnssecarray[] = $record->rdata . PHP_EOL;
                    } else {
                        $dnssecarray[] = $record->rdata->keydata . PHP_EOL;
                    }
                }
            }
        } catch (Net_DNS2_Exception $e) {
            //echo "Error: " . $e->getMessage();
        }
    
        return $dnssecarray;
    }
    
    // Get DS
    function get_ds($toproof) {

        $resolver = new Resolver();
        $resolver->dnssec = true;
    
        $dsarray = [];
    
        try {
            $response = $resolver->query($toproof, 'DS');
            
            foreach ($response->answer as $record) {
                if ($record->type === 'DS') {
                    if (is_string($record->rdata)) {
                        $dsarray[] = $record->rdata . PHP_EOL;
                    } else {
                        $dsarray[] = $record->rdata->keydata . PHP_EOL;
                    }
                }
            }
        } catch (Net_DNS2_Exception $e) {
            //echo "Error: " . $e->getMessage();
        }
    
        return $dsarray;
        
        
    }


    // Get RRSIG

    function get_rrsig($toproof) {
        
        $resolver = new Resolver();
        $resolver->dnssec = true;
    
        $rrsigarray = [];
    
        try {
            $response = $resolver->query($toproof, 'RRSIG');
            
            foreach ($response->answer as $record) {
                if ($record->type === 'RRSIG') {
                    if (is_string($record->rdata)) {
                        $rrsigarray[] = $record->rdata . PHP_EOL;
                    } else {
                        $rrsigarray[] = $record->rdata->keydata . PHP_EOL;
                    }
                }
            }
        } catch (Net_DNS2_Exception $e) {
            //echo "Error: " . $e->getMessage();
        }
    
        return $rrsigarray;

    }

    //print the DNSSEC

    function print_dnssec($dnsKey, $ds, $rrsig, $dnssec_enabled, $dnssec_validate) {
        ?>
        <div class="alert alert-danger">
            <strong>Attention!</strong> This function is still under development and has not yet been sufficiently tested. It is recommended to check the results with another tool. For example: <a href="https://dnssec-analyzer.verisignlabs.com/" class="alert-link">DNSSEC-Analyzer from Verisignlabs.com</a>.
        </div>
        <?php

        echo '</br>';
        echo '<h4>';

        if ( $dnssec_enabled === true ) {
            ?>
                <span class="badge bg-success">DNSSEC enabled</span>
            <?php
        } else {
            ?>
                <span class="badge bg-danger">DNSSEC not enabled</span>
            <?php
        }

        if ( $dnssec_validate === true ) {

            ?> 
                <span class="badge bg-success">DNSSEC validated</span>
            <?php
        } else {
            ?>
                <span class="badge bg-danger">DNSSEC not validated</span>
            <?php

        }
        echo '</br>';
        echo '</br>';

        if (count($dnsKey) > 0) {
            ?>
                <span class="badge bg-success">DNSKEY found</span>
            <?php

        } else {
            ?>
                <span class="badge bg-danger">DNSKEY not found</span>
            <?php
        }
        if (count($ds) > 0) {
            ?>
                <span class="badge bg-success">DS found</span>
            <?php
        } else {
            ?>
                <span class="badge bg-danger">DS not found</span>
            <?php    
        }

        if (count($rrsig) > 0) {

            ?>
                <span class="badge bg-success">RRSIG found</span>
            <?php

        } else {

            ?>
                <span class="badge bg-danger">RRSIG not found</span>
            <?php
        }
        echo '</h4>';

    }
?> 
