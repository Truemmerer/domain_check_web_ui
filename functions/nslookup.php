<?php

    require_once 'Provider/cloudflare.php';
    require_once 'Provider/google.php';
    require_once 'Provider/opendns.php';
    require_once 'Provider/authoritative.php';




    function nslookup($toproof) {
      
        list($cloudflare_ipv4_addresses, $cloudflare_ipv6_addresses, $cloudflare_txt_records, $cloudflare_cname_records, $cloudflare_mx_records, $cloudflare_ns_records) = cloudflare_check($toproof);    
        list($google_ipv4_addresses, $google_ipv6_addresses, $google_txt_records, $google_cname_records, $google_mx_records, $google_ns_records) = google_check($toproof);   
        list($opendns_ipv4_addresses, $opendns_ipv6_addresses, $opendns_txt_records, $opendns_cname_records, $opendns_mx_records, $opendns_ns_records) = opendns_check($toproof);    
        list($authoritative_ipv4_addresses, $authoritative_ipv6_addresses, $authoritative_txt_records, $authoritative_cname_records, $authoritative_mx_records, $authoritative_ns_records, $authoritative_ip) = authoritative_check($toproof);    


    
        build_nslookup($cloudflare_ipv4_addresses, $cloudflare_ipv6_addresses, $cloudflare_txt_records, $cloudflare_cname_records, $cloudflare_mx_records, $cloudflare_ns_records, $google_ipv4_addresses, $google_ipv6_addresses, $google_txt_records, $google_cname_records, $google_mx_records, $google_ns_records, $opendns_ipv4_addresses, $opendns_ipv6_addresses, $opendns_txt_records, $opendns_cname_records, $opendns_mx_records, $opendns_ns_records, $authoritative_ipv4_addresses, $authoritative_ipv6_addresses, $authoritative_txt_records, $authoritative_cname_records, $authoritative_mx_records, $authoritative_ns_records, $authoritative_ip);
    }

 
    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // OUTPUT 
    function build_nslookup($cloudflare_ipv4_addresses, $cloudflare_ipv6_addresses, $cloudflare_txt_records, $cloudflare_cname_records, $cloudflare_mx_records, $cloudflare_ns_records, $google_ipv4_addresses, $google_ipv6_addresses, $google_txt_records, $google_cname_records, $google_mx_records, $google_ns_records, $opendns_ipv4_addresses, $opendns_ipv6_addresses, $opendns_txt_records, $opendns_cname_records, $opendns_mx_records, $opendns_ns_records, $authoritative_ipv4_addresses, $authoritative_ipv6_addresses, $authoritative_txt_records, $authoritative_cname_records, $authoritative_mx_records, $authoritative_ns_records, $authoritative_ip) {
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
                            <div class="card-body">
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
