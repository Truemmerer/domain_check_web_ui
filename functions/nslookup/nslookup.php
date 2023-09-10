<?php

    require_once 'nslookup/custom_nameserver.php';
    require_once 'nslookup/authoritative.php';

    function nslookup($toproof) {
      
        
            $nameserver_array = nameserver_check($toproof);            
            $dns_array_result_authoritative = authoritative_check($toproof);    


            return [$nameserver_array, $dns_array_result_authoritative]; 

        
    }

 
    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // OUTPUT 
    function build_nslookup($toproof) {

        list($nameserver_array, $dns_array_result_authoritative) = nslookup ($toproof);

        // Check if the DNS are different each other
        list($dns_diff, $ipv4_diff, $ipv6_diff, $txt_diff, $cname_diff, $mx_diff, $ns_diff) = dns_check_different($nameserver_array, $dns_array_result_authoritative);
        // Check if DNS not same
        if ($dns_diff === true) {
            // Check if IPv4 same 
            if ($ipv4_diff === true) {
                ?>
                    <div class="alert alert-warning">
                        <strong>Warning!</strong> The IPv4 addresses differ from each other! <br/> Please note that changes take up to 48 hours to be distributed worldwide. This error message can therefore be ignored if the DNS have been set recently.</a>.
                    </div>
                <?php
            }
            // Check if IPv6 same
            if ($ipv6_diff === true) {
                ?>
                    <div class="alert alert-warning">
                        <strong>Warning!</strong> The IPv6 addresses differ from each other! <br/> Please note that changes take up to 48 hours to be distributed worldwide. This error message can therefore be ignored if the DNS have been set recently.</a>.
                    </div>
                <?php
            }
            // Check if txt same 
            if ($txt_diff === true) {
                ?>
                    <div class="alert alert-warning">
                        <strong>Warning!</strong> The TXT records differ from each other! <br/> Please note that changes take up to 48 hours to be distributed worldwide. This error message can therefore be ignored if the DNS have been set recently.</a>.
                    </div>
                <?php
            }
            // Check if cname same 
            if ($cname_diff === true) {
                ?>
                    <div class="alert alert-warning">
                        <strong>Warning!</strong> The CNAME records differ from each other! <br/> Please note that changes take up to 48 hours to be distributed worldwide. This error message can therefore be ignored if the DNS have been set recently.</a>.
                    </div>
                <?php
            }
            // Check if mx same
            if ($mx_diff === true) {
                ?>
                    <div class="alert alert-warning">
                        <strong>Warning!</strong> The MX records differ from each other! <br/> Please note that changes take up to 48 hours to be distributed worldwide. This error message can therefore be ignored if the DNS have been set recently.</a>.
                    </div>
                <?php
            }
            // Check if ns same
            if ($ns_diff === true) {
                ?>
                    <div class="alert alert-warning">
                        <strong>Warning!</strong> The NS records differ from each other! <br/> Please note that changes take up to 48 hours to be distributed worldwide. This error message can therefore be ignored if the DNS have been set recently.</a>.
                    </div>
                <?php
            }
        }
        ?>
  


        <!-- Build the main output -->
        <br>
        <br>
        <div class="alert alert-dark">
            <strong>Info:</strong> The following nameservers are the authoritative name servers:
        </div>

        <?php
        foreach ($dns_array_result_authoritative as $result) {
            $ns_ip = $result['ns_ip'];
            $ns_name = $result['ns_name'];
            $ipv4 = $result['ipv4'];
            $ipv6 = $result['ipv6'];
            $txt = $result['txt'];
            $cname = $result['cname'];
            $mx = $result['mx'];
            $ns = $result['ns'];

            $collapseId = 'collapse' . ucfirst(str_replace(['_', '.'], '', $ns_name));

            echo '<div class="card card-box-style">';
            echo '<div class="card-header" role="button" data-bs-toggle="collapse" data-bs-target="#' . $collapseId . '" aria-expanded="false" aria-controls="' . $collapseId . '">';
            echo '<h2 class="mb-0">' . $ns_name . ' (' . $ns_ip . ')</h2>';
            echo '</div>';
            echo '<div class="collapse" id="' . $collapseId . '">';
            echo '<div class="card-body card-body-style">';
            echo '<div class="container-fluid">';
            echo '<div class="card">';
            echo '<div class="card-body card-body-style">';
            echo '<h4>A Records (IPv4)</h4>';
            if (!empty($ipv4)) {
                foreach ($ipv4 as $ip) {
                    echo $ip . "<br>";
                }
            }
            echo '</div>';
            echo '</div>';
            echo '<div class="card">';
            echo '<div class="card-body card-body-style">';
            echo '<h4>AAAA Records (IPv6)</h4>';
            if (!empty($ipv6)) {
                foreach ($ipv6 as $ip) {
                    echo $ip . "<br>";
                }
            }
            echo '</div>';
            echo '</div>';
            echo '<div class="card">';
            echo '<div class="card-body card-body-style">';
            echo '<h4>CNAME Records</h4>';
            if (!empty($cname)) {
                foreach ($cname as $record) {
                    echo $record . "<br>";
                }
            }
            echo '</div>';
            echo '</div>';
            echo '<div class="card">';
            echo '<div class="card-body card-body-style">';
            echo '<h4>TXT Records</h4>';
            if (!empty($txt)) {
                foreach ($txt as $record) {
                    echo $record . "<br>";
                }
            }
            echo '</div>';
            echo '</div>';
            echo '<div class="card">';
            echo '<div class="card-body card-body-style">';
            echo '<h4>MX Records</h4>';
            if (!empty($mx)) {
                foreach ($mx as $record) {
                    echo $record . "<br>";
                }
            }
            echo '</div>';
            echo '</div>';
            echo '<div class="card">';
            echo '<div class="card-body card-body-style">';
            echo '<h4>NS Records</h4>';
            if (!empty($ns)) {
                foreach ($ns as $record) {
                    echo $record . "<br>";
                }
            }
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }

        ?>
            <br>
            <br>
            <div class="alert alert-dark">
            <strong>Info:</strong> The following nameservers were also checked:
            </div>
        <?php

        foreach ($nameserver_array as $result) {
            $ns_ip = $result['ns_ip'];
            $ns_name = $result['ns_name'];
            $ipv4 = $result['ipv4'];
            $ipv6 = $result['ipv6'];
            $txt = $result['txt'];
            $cname = $result['cname'];
            $mx = $result['mx'];
            $ns = $result['ns'];

            $collapseId = 'collapse' . ucfirst(str_replace(['_', '.'], '', $ns_name));

            echo '<div class="card card-box-style">';
            echo '<div class="card-header" role="button" data-bs-toggle="collapse" data-bs-target="#' . $collapseId . '" aria-expanded="false" aria-controls="' . $collapseId . '">';
            echo '<h2 class="mb-0">' . $ns_name . ' (' . $ns_ip . ')</h2>';
            echo '</div>';
            echo '<div class="collapse" id="' . $collapseId . '">';
            echo '<div class="card-body card-body-style">';
            echo '<div class="container-fluid">';
            echo '<div class="card">';
            echo '<div class="card-body card-body-style">';
            echo '<h4>A Records (IPv4)</h4>';
            if (!empty($ipv4)) {
                foreach ($ipv4 as $ip) {
                    echo $ip . "<br>";
                }
            }
            echo '</div>';
            echo '</div>';
            echo '<div class="card">';
            echo '<div class="card-body card-body-style">';
            echo '<h4>AAAA Records (IPv6)</h4>';
            if (!empty($ipv6)) {
                foreach ($ipv6 as $ip) {
                    echo $ip . "<br>";
                }
            }
            echo '</div>';
            echo '</div>';
            echo '<div class="card">';
            echo '<div class="card-body card-body-style">';
            echo '<h4>CNAME Records</h4>';
            if (!empty($cname)) {
                foreach ($cname as $record) {
                    echo $record . "<br>";
                }
            }
            echo '</div>';
            echo '</div>';
            echo '<div class="card">';
            echo '<div class="card-body card-body-style">';
            echo '<h4>TXT Records</h4>';
            if (!empty($txt)) {
                foreach ($txt as $record) {
                    echo $record . "<br>";
                }
            }
            echo '</div>';
            echo '</div>';
            echo '<div class="card">';
            echo '<div class="card-body card-body-style">';
            echo '<h4>MX Records</h4>';
            if (!empty($mx)) {
                foreach ($mx as $record) {
                    echo $record . "<br>";
                }
            }
            echo '</div>';
            echo '</div>';
            echo '<div class="card">';
            echo '<div class="card-body card-body-style">';
            echo '<h4>NS Records</h4>';
            if (!empty($ns)) {
                foreach ($ns as $record) {
                    echo $record . "<br>";
                }
            }
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    
    }

?>
        
        