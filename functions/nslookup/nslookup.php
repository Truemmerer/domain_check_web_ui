<?php

    require_once 'functions/nslookup/custom_nameserver.php';
    require_once 'functions/nslookup/authoritative.php';

    function nslookup($toproof) {
            $whatisit = whatisit($toproof);

            if ($whatisit === 2) {

                // Check if Domain is in IDN
                if ( is_idn($toproof) ) {
                    $toproof = idn_to_puny($toproof);
                }

                $nameserver_array = nameserver_check($toproof);            
                $dns_array_result_authoritative = authoritative_check($toproof);    

                build_nslookup($nameserver_array, $dns_array_result_authoritative);

            } else {     
                ?>
                <div class="alert alert-info">
                    <strong>Note:</strong> nslookup works only with a domain</a>.
                </div>
                <?php
            }
        
    }

 
    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // OUTPUT 
    function build_nslookup($nameserver_array, $dns_array_result_authoritative) {

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
        <div class="alert card-box-nslookup-separator">
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
            echo '<h4>A Records (IPv4)';
            aModal();
            echo '</h4>';
            if (!empty($ipv4)) {
                foreach ($ipv4 as $ip) {
                    echo $ip . "<br>";
                }
            }
            echo '</div>';
            echo '</div>';
            echo '<div class="card">';
            echo '<div class="card-body card-body-style">';
            echo '<h4>AAAA Records (IPv6)';
            aaaaModal();
            echo '</h4>';
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
            echo '<h4>TXT Records';
            txtModal();
            echo '</h4>';
            if (!empty($txt)) {
                foreach ($txt as $record) {
                    echo $record . "<br>";
                }
            }
            echo '</div>';
            echo '</div>';
            echo '<div class="card">';
            echo '<div class="card-body card-body-style">';
            echo '<h4>MX Records';
            mxModal();
            echo '</h4>';
            if (!empty($mx)) {
                mx_table($mx);
            }
            echo '</div>';
            echo '</div>';
            echo '<div class="card">';
            echo '<div class="card-body card-body-style">';
            echo '<h4>NS Records';
            nsModal();
            echo '</h4>';
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
            <div class="alert card-box-nslookup-separator">
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
            echo '<h4>A Records (IPv4)';
            aModal();
            echo '</h4>';
            if (!empty($ipv4)) {
                foreach ($ipv4 as $ip) {
                    echo $ip . "<br>";
                }
            }
            echo '</div>';
            echo '</div>';
            echo '<div class="card">';
            echo '<div class="card-body card-body-style">';
            echo '<h4>AAAA Records (IPv6)';
            aaaaModal();
            echo '</h4>';
            if (!empty($ipv6)) {
                foreach ($ipv6 as $ip) {
                    echo $ip . "<br>";
                }
            }
            echo '</div>';
            echo '</div>';
            echo '<div class="card">';
            echo '<div class="card-body card-body-style">';
            echo '<h4>CNAME Records';
            cnameModal();
            echo '</h4>';
            if (!empty($cname)) {
                foreach ($cname as $record) {
                    echo $record . "<br>";
                }
            }
            echo '</div>';
            echo '</div>';
            echo '<div class="card">';
            echo '<div class="card-body card-body-style">';
            echo '<h4>TXT Records';
            txtModal();
            echo '</h4>';
            if (!empty($txt)) {
                foreach ($txt as $record) {
                    echo $record . "<br>";
                }
            }
            echo '</div>';
            echo '</div>';
            echo '<div class="card">';
            echo '<div class="card-body card-body-style">';
            echo '<h4>MX Records ';
            mxModal();
            echo '</h4>';
            if (!empty($mx)) {
                mx_table($mx);
            }
            echo '</div>';
            echo '</div>';
            echo '<div class="card">';
            echo '<div class="card-body card-body-style">';
            echo '<h4>NS Records';
            nsModal();
            echo '</h4>';
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


            // MODAL with explanation of dns-records
            ?>
                <div class="modal" id="mxModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
        
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">MX-Records</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
        
                        <!-- Modal body -->
                        <div class="modal-body">
                        <?php
        
                            mxExplain();
        
                        ?>
                        </div>
        
                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                        </div>
                        
                        </div>
                    </div>
                </div>

                <div class="modal" id="aaaaModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
        
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">AAAA-Records</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
        
                        <!-- Modal body -->
                        <div class="modal-body">
                        <?php
        
                            aaaaExplain();
        
                        ?>
                        </div>
        
                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                        </div>
                        
                        </div>
                    </div>
                </div>

                <div class="modal" id="aModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
        
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">A-Records</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
        
                        <!-- Modal body -->
                        <div class="modal-body">
                        <?php
        
                            aExplain();
        
                        ?>
                        </div>
        
                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                        </div>
                        
                        </div>
                    </div>
                </div>

                <div class="modal" id="cnameModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
        
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">CNAME-Records</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
        
                        <!-- Modal body -->
                        <div class="modal-body">
                        <?php
        
                            cnameExplain();
        
                        ?>
                        </div>
        
                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                        </div>
                        
                        </div>
                    </div>
                </div>

                <div class="modal" id="nsModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
        
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">NS-Records</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
        
                        <!-- Modal body -->
                        <div class="modal-body">
                        <?php
        
                            nsExplain();
        
                        ?>
                        </div>
        
                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                        </div>
                        
                        </div>
                    </div>
                </div>

                <div class="modal" id="txtModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
        
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">TXT-Records</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
        
                        <!-- Modal body -->
                        <div class="modal-body">
                        <?php
        
                            txtExplain();
        
                        ?>
                        </div>
        
                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                        </div>
                        
                        </div>
                    </div>
                </div>
            <?php
        }
    
    }

?>
        
<?php    
    function mx_table($mx) {
        echo '<div class="row">';
        echo '<div class="col">';
        echo '<table class="table table-dark table-borderless">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Destination</th>';
        echo '<th>Priority</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($mx as $record) {
            echo '<tr>';
            echo '<td>' . $record['destination'] . '</td>';
            echo '<td>' . $record['priority'] . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '<div class="col">';
        echo '</div>';
        echo '<div class="col">';
        echo '</div>';
        echo '</div>';
    }
?>