<?php

    require_once 'Provider/cloudflare.php';
    require_once 'Provider/google.php';
    require_once 'Provider/opendns.php';
    require_once 'Provider/authoritative.php';
    require_once 'check_dns.php';



    function nslookup($toproof) {
      
        if (filter_var($toproof, FILTER_VALIDATE_IP)) {

            $rdns = gethostbyaddr($toproof);

            if ($rdns !== false) {

                $ip_rdns = $toproof;
                $toproof = $rdns;

                ?>
                    <div class="alert alert-info">
                        <strong>Reverse DNS (rDNS): </strong><?php echo $toproof ?></a>.
                    </div>
                <?php

            } else {

                ?>
                <div class="alert alert-info">
                    <strong>Note!</strong> The IP has no reverse DNS (rDNS)</a>.
                </div>
                <?php

                exit;

            }
            
        }
        
            list($cloudflare_ipv4_addresses, $cloudflare_ipv6_addresses, $cloudflare_txt_records, $cloudflare_cname_records, $cloudflare_mx_records, $cloudflare_ns_records) = cloudflare_check($toproof);    
            list($google_ipv4_addresses, $google_ipv6_addresses, $google_txt_records, $google_cname_records, $google_mx_records, $google_ns_records) = google_check($toproof);   
            list($opendns_ipv4_addresses, $opendns_ipv6_addresses, $opendns_txt_records, $opendns_cname_records, $opendns_mx_records, $opendns_ns_records) = opendns_check($toproof);    
            list($authoritative_ipv4_addresses, $authoritative_ipv6_addresses, $authoritative_txt_records, $authoritative_cname_records, $authoritative_mx_records, $authoritative_ns_records, $authoritative_ip) = authoritative_check($toproof);    

            if (in_array($ip_rdns, $authoritative_ipv4_addresses) || in_array($ip_rdns, $authoritative_ipv6_addresses)) {
                ?>
                <div class="alert alert-info">
                    <strong>PTR: </strong>A PTR is set. The IP points to <?php echo $toproof ?> (rDNS), which in turn points to the IP <?php echo $ip_rdns ?> (DNS).</a>
                </div>
                <?php
            }

            build_nslookup($cloudflare_ipv4_addresses, $cloudflare_ipv6_addresses, $cloudflare_txt_records, $cloudflare_cname_records, $cloudflare_mx_records, $cloudflare_ns_records, $google_ipv4_addresses, $google_ipv6_addresses, $google_txt_records, $google_cname_records, $google_mx_records, $google_ns_records, $opendns_ipv4_addresses, $opendns_ipv6_addresses, $opendns_txt_records, $opendns_cname_records, $opendns_mx_records, $opendns_ns_records, $authoritative_ipv4_addresses, $authoritative_ipv6_addresses, $authoritative_txt_records, $authoritative_cname_records, $authoritative_mx_records, $authoritative_ns_records, $authoritative_ip);
    

        
    }

 
    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // OUTPUT 
    function build_nslookup($cloudflare_ipv4_addresses, $cloudflare_ipv6_addresses, $cloudflare_txt_records, $cloudflare_cname_records, $cloudflare_mx_records, $cloudflare_ns_records, $google_ipv4_addresses, $google_ipv6_addresses, $google_txt_records, $google_cname_records, $google_mx_records, $google_ns_records, $opendns_ipv4_addresses, $opendns_ipv6_addresses, $opendns_txt_records, $opendns_cname_records, $opendns_mx_records, $opendns_ns_records, $authoritative_ipv4_addresses, $authoritative_ipv6_addresses, $authoritative_txt_records, $authoritative_cname_records, $authoritative_mx_records, $authoritative_ns_records, $authoritative_ip) {
        ?>

        <!-- Check if IPv4 same -->
       
       <?php 
            if (ipv4_check($cloudflare_ipv4_addresses, $google_ipv4_addresses, $opendns_ipv4_addresses, $authoritative_ipv4_addresses) == false) {
                ?>
                    <div class="alert alert-warning">
                        <strong>Warning!</strong> The IPv4 addresses differ from each other! <br/> Please note that changes take up to 48 hours to be distributed worldwide. This error message can therefore be ignored if the DNS have been set recently.</a>.
                    </div>
                <?php
            }
        ?>

        <div class="card card-box-style">
            <div class="card-header" role="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                <h2 class="mb-0">Authoritative </h2> (              
                <?php 
                    echo $authoritative_ip;              
                ?>
                )
            </div>
            <div class="collapse" id="collapseOne">
                <div class="card-body card-body-style">
                    <div class="container-fluid">
                    <div class="card">
                            <div class="card-body card-body-style">
                                <h4>A Records (IPv4)</h4>
                                    <?php 
                                        if (!empty($authoritative_ipv4_addresses)) {
                                            foreach ($authoritative_ipv4_addresses as $ip) {
                                                echo $ip . "<br>";
                                            }
                                        }
                                    ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body card-body-style">
                                 <h4>AAAA Records (IPv6)</h4>
                                    <?php 
                                        if (!empty($authoritative_ipv6_addresses)) {
                                            foreach ($authoritative_ipv6_addresses as $ip) {
                                                echo $ip . "<br>";
                                            }
                                        }
                                    ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body card-body-style">
                                <h4>CNAME Records</h4>
                                <?php
                                    if (!empty($authoritative_cname_records)) {
                                        foreach ($authoritative_cname_records as $record) {
                                            echo "$record<br>";
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body card-body-style">
                                <h4>TXT Records</h4>
                                    <?php
                                        if (!empty($authoritative_txt_records)) {
                                            foreach ($authoritative_txt_records as $record) {
                                                echo "$record<br>";
                                            }
                                        }
                                    ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body card-body-style">
                                <h4>MX Records</h4>
                                    <?php
                                        if (!empty($authoritative_mx_records)) {
                                            foreach ($authoritative_mx_records as $record) {
                                                echo "$record<br>";
                                            }
                                        }
                                    ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body card-body-style">
                                <h4>NS Records</h4>
                                <?php
                                        if (!empty($authoritative_ns_records)) {
                                            foreach ($authoritative_ns_records as $record) {
                                                echo "$record<br>";
                                            }
                                        }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card card-box-style">
            <div class="card-header" role="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                <h2 class="mb-0">Cloudflare</h2>
            </div>
            <div class="collapse" id="collapseTwo">
                <div class="card-body card-body-style">
                    <div class="container-fluid">
                        <div class="card">
                            <div class="card-body card-body-style">
                                <h4>A Records (IPv4)</h4>
                                    <?php 
                                        if (!empty($cloudflare_ipv4_addresses)) {
                                            foreach ($cloudflare_ipv4_addresses as $ip) {
                                                echo $ip . "<br>";
                                            }
                                        }
                                    ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body card-body-style">
                                 <h4>AAAA Records (IPv6)</h4>
                                    <?php 
                                        if (!empty($cloudflare_ipv6_addresses)) {
                                            foreach ($cloudflare_ipv6_addresses as $ip) {
                                                echo $ip . "<br>";
                                            }
                                        }
                                    ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body card-body-style">
                                <h4>CNAME Records</h4>
                                <?php
                                    if (!empty($cloudflare_cname_records)) {
                                        foreach ($cloudflare_cname_records as $record) {
                                            echo "$record<br>";
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body card-body-style">
                                <h4>TXT Records</h4>
                                    <?php
                                        if (!empty($cloudflare_txt_records)) {
                                            foreach ($cloudflare_txt_records as $record) {
                                                echo "$record<br>";
                                            }
                                        }
                                    ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body card-body-style">
                                <h4>MX Records</h4>
                                    <?php
                                        if (!empty($cloudflare_mx_records)) {
                                            foreach ($cloudflare_mx_records as $record) {
                                                echo "$record<br>";
                                            }
                                        }
                                    ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body card-body-style">
                                <h4>NS Records</h4>
                                <?php
                                        if (!empty($cloudflare_ns_records)) {
                                            foreach ($cloudflare_ns_records as $record) {
                                                echo "$record<br>";
                                            }
                                        }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card card-box-style">
            <div class="card-header" role="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
               <h2 class="mb-0">Google</h2>
            </div>
            <div class="collapse" id="collapseThree">
                <div class="card-body card-body-style">
                <div class="container-fluid">
                        <div class="card">
                            <div class="card-body card-body-style">
                                <h4>A Records (IPv4)</h4>
                                    <?php 
                                        if (!empty($google_ipv4_addresses)) {
                                            foreach ($google_ipv4_addresses as $ip) {
                                                echo $ip . "<br>";
                                            }
                                        }
                                    ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body card-body-style">
                                 <h4>AAAA Records (IPv6)</h4>
                                    <?php 
                                        if (!empty($google_ipv6_addresses)) {
                                            foreach ($google_ipv6_addresses as $ip) {
                                                echo $ip . "<br>";
                                            }
                                        }
                                    ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body card-body-style">
                                <h4>CNAME Records</h4>
                                <?php
                                    if (!empty($google_cname_records)) {
                                        foreach ($google_cname_records as $record) {
                                            echo "$record<br>";
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body card-body-style">
                                <h4>TXT Records</h4>
                                    <?php
                                        if (!empty($google_txt_records)) {
                                            foreach ($google_txt_records as $record) {
                                                echo "$record<br>";
                                            }
                                        }
                                    ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body card-body-style">
                                <h4>MX Records</h4>
                                    <?php
                                        if (!empty($google_mx_records)) {
                                            foreach ($google_mx_records as $record) {
                                                echo "$record<br>";
                                            }
                                        }
                                    ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body card-body-style">
                                <h4>NS Records</h4>
                                <?php
                                        if (!empty($google_ns_records)) {
                                            foreach ($google_ns_records as $record) {
                                                echo "$record<br>";
                                            }
                                        }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card card-box-style">
            <div class="card-header" role="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                <h2 class="mb-0">Open DNS</h2>
            </div>
            <div class="collapse" id="collapseFour">
                <div class="card-body card-body-style">
                <div class="container-fluid">
                <div class="card">
                            <div class="card-body card-body-style">
                                <h4>A Records (IPv4)</h4>
                                    <?php 
                                        if (!empty($opendns_ipv4_addresses)) {
                                            foreach ($opendns_ipv4_addresses as $ip) {
                                                echo $ip . "<br>";
                                            }
                                        }
                                    ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body card-body-style">
                                 <h4>AAAA Records (IPv6)</h4>
                                    <?php 
                                        if (!empty($opendns_ipv6_addresses)) {
                                            foreach ($opendns_ipv6_addresses as $ip) {
                                                echo $ip . "<br>";
                                            }
                                        }
                                    ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body card-body-style">
                                <h4>CNAME Records</h4>
                                <?php
                                    if (!empty($opendns_cname_records)) {
                                        foreach ($opendns_cname_records as $record) {
                                            echo "$record<br>";
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body card-body-style">
                                <h4>TXT Records</h4>
                                    <?php
                                        if (!empty($opendns_txt_records)) {
                                            foreach ($opendns_txt_records as $record) {
                                                echo "$record<br>";
                                            }
                                        }
                                    ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body card-body-style">
                                <h4>MX Records</h4>
                                    <?php
                                        if (!empty($opendns_mx_records)) {
                                            foreach ($opendns_mx_records as $record) {
                                                echo "$record<br>";
                                            }
                                        }
                                    ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body card-body-style">
                                <h4>NS Records</h4>
                                <?php
                                        if (!empty($opendns_ns_records)) {
                                            foreach ($opendns_ns_records as $record) {
                                                echo "$record<br>";
                                            }
                                        }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

?>
