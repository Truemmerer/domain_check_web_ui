<?php

    require_once 'src/dnsexplain.php';

    function dns_check($toproof, $tld) {

        // Get authorative nameservers
        $authorativelist = get_authorative_nameservers($toproof, $tld);

        // Import the nameserverlist.yaml as array
        $yamlContent = file_get_contents("nameservers.yaml");
        $customns = yaml_parse($yamlContent);

        // convert custom nameservers in correct format
        $custom_ns_format = array();
        foreach ($customns as $name => $ip) {
            $custom_ns_format[] = array(
                "ns_name" => $name,
                "ns_ip" => $ip,
                "type" => "custom"
            );
        }

        // Merge authorative nameservers and custom nameservers to one array
        $nameservers = array_merge($authorativelist, $custom_ns_format);


        // Get default dns
        $dns_array = get_default_dns($toproof, $nameservers);

        //header('Content-Type: application/json');
        //echo json_encode($dns_array);
        
        // Print DNS
        print_dns_check($dns_array);        


    }

    function get_default_dns($toproof, $nameserverlist) {
        $dns_array_result = [];

        foreach ($nameserverlist as $nameserver) {

            $ns_ip = $nameserver['ns_ip'];
            $ns_name = $nameserver['ns_name'];
            $type = $nameserver['type'];

            $ipv4_addresses = [];
            $ipv6_addresses = [];
            $txt_adresses = [];
            $cname_adresses = [];
            $mx_addresses = [];
            $ns_adresses = [];

            
            //-----------------------------------------------------------------------------------
            // IPv4 Records

            $command = "dig +short A " . escapeshellcmd($toproof) . " @" . escapeshellcmd($ns_ip) . " | sort";
            $output = shell_exec($command);
            $ipv4_a = explode("\n", $output);
            foreach ($ipv4_a as $ipv4_entry) {
                if (trim($ipv4_entry) !== '') {
                    $ipv4_addresses[] = $ipv4_entry;
                }
            }

            //-----------------------------------------------------------------------------------
            // IPv6 Records
            $command = "dig +short AAAA " . escapeshellcmd($toproof) . " @" . escapeshellcmd($ns_ip) . " | sort";
            $output = shell_exec($command);
            $ipv6_a = explode("\n", $output);
            foreach ($ipv6_a as $ipv6_entry) {
                if (trim($ipv6_entry) !== '') {
                    $ipv6_addresses[] = $ipv6_entry;
                }
            }


            //-----------------------------------------------------------------------------------
            // TXT Records 

            $command = "dig +short TXT " . escapeshellcmd($toproof) . " @" . escapeshellcmd($ns_ip) . " | sort";
            $output = shell_exec($command);
            $txt_a = explode("\n", $output);
            foreach ($txt_a as $txt_entry) {
                if (trim($txt_entry) !== '') {
                    $txt_adresses[] = $txt_entry;
                }
            }

            //-----------------------------------------------------------------------------------
            // CNAME Records 
            $command = "dig +short CNAME " . escapeshellcmd($toproof) . " @" . escapeshellcmd($ns_ip) . " | sort";
            $output = shell_exec($command);
            $cname_a = explode("\n", $output);
            foreach ($cname_a as $cname_entry) {
                if (trim($cname_entry) !== '') {
                    $cname_adresses[] = $cname_entry;
                }
            }

            //-----------------------------------------------------------------------------------
            // MX Records
            $command = "dig +short MX " . escapeshellcmd($toproof) . " @" . escapeshellcmd($ns_ip) . " | sort";
            $output = shell_exec($command);
            $mx_a = explode("\n", $output);
            foreach ($mx_a as $mx_entry) {
                if (trim($mx_entry) !== '') {
                    $parts = explode(' ', $mx_entry);
                    $priority = (int)$parts[0];
                    $destination = $parts[1];
                    $mx_addresses[] = array('priority' => $priority, 'destination' => $destination);
                }
            }


            $dns_array_result[] = [
                'ns_ip' => $ns_ip,
                'ns_name' => $ns_name,
                'type' => $type,
                'ipv4' => $ipv4_addresses,
                'ipv6' => $ipv6_addresses,
                'txt' => $txt_adresses,
                'cname' => $cname_adresses,
                'mx' => $mx_addresses,
            ];

        } // END of foreach


        return $dns_array_result;
    }

    function print_dns_check($dns_array) {
        foreach ($dns_array as $result) {
            $ns_ip = $result['ns_ip'];
            $ns_name = $result['ns_name'];
            $ipv4 = $result['ipv4'];
            $ipv6 = $result['ipv6'];
            $txt = $result['txt'];
            $cname = $result['cname'];
            $mx = $result['mx'];
            // $ns = $result['ns'];
            
            echo '<div class="content-header">' . $ns_name . ' (' . $ns_ip . ')</div>';

            echo '<table class="table table-borderless">';
            echo '<thead class="table-head-background table-head-bigfont">';
            echo '<tr>';
            echo '<th>IPv4</th>';
            ?>
            <th>
                <button type="button" class="btn table-info btn-sm" data-bs-toggle="modal" data-bs-target="#aModal">
                    <img src="assets/feather/info.svg" alt="Info" width="32" height="16">
                </button>
            </th>
            <?php
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            if (empty($ipv4)) {
                echo '<tr>';
                echo "<th>-</th>";
                echo '<th></th>';
                echo '</tr>';
            } else {
                foreach ($ipv4 as $ipv4_entry) {
                    echo '<tr>';
                    echo '<th>' . $ipv4_entry . '</th>';
                    echo '<th></th>';
                    echo '</tr>';
                }
            }
            echo '</tbody>';

            // IPv6
            echo '<thead class="table-head-background table-head-bigfont">';
            echo '<tr>';
            echo '<th>IPv6</th>';
            ?>
            <th>
                <button type="button" class="btn table-info btn-sm" data-bs-toggle="modal" data-bs-target="#aaaaModal">
                    <img src="assets/feather/info.svg" alt="Info" width="32" height="16">
                </button>
            </th>
            <?php
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            if (empty($ipv6)) {
                echo '<tr>';
                echo "<th>-</th>";
                echo '<th></th>';
                echo '</tr>';
            } else {
                foreach ($ipv6 as $ipv6_entry) {
                    echo '<tr>';
                    echo '<th>' . $ipv6_entry . '</th>';
                    echo '<th></th>';
                    echo '</tr>';
                }
            }
            echo '</tbody>';

            // TXT
            echo '<thead class="table-head-background table-head-bigfont">';
            echo '<tr>';
            echo '<th>TXT</th>';
           ?>
           <th>
                <button type="button" class="btn table-info btn-sm" data-bs-toggle="modal" data-bs-target="#txtModal">
                    <img src="assets/feather/info.svg" alt="Info" width="32" height="16">
                </button>
            </th>
            <?php
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            if (empty($txt)) {
                echo '<tr>';
                echo "<th>-</th>";
                echo '<th></th>';
                echo '</tr>';
            } else {
                foreach ($txt as $txt_entry) {
                    echo '<tr>';
                    echo "<th>" . $txt_entry . "</th>";
                    echo '<th></th>';
                    echo '</tr>';
                }
            }
            echo '</tbody>';

            // CNAME
            echo '<thead class="table-head-background table-head-bigfont">';
            echo '<tr>';
            echo '<th>CNAME</th>';
            ?>
            <th>
                <button type="button" class="btn table-info btn-sm" data-bs-toggle="modal" data-bs-target="#cnameModal">
                    <img src="assets/feather/info.svg" alt="Info" width="32" height="16">
                </button>
            </th>
            <?php
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            if (empty($cname)) {
                echo '<tr>';
                echo "<th>-</th>";
                echo '<th></th>';
                echo '</tr>';
            } else {
                foreach ($cname as $cname_entry) {
                    echo '<tr>';
                    echo "<th>" . $cname_entry . "</th>";
                    echo '<th></th>';
                    echo '</tr>';
                }
            }
            echo '</tbody>';

            // MX
            echo '<thead class="table-head-background table-head-bigfont">';
            echo '<tr>';
            echo '<th>MX</th>';
            ?>
            <th>
                <button type="button" class="btn table-info btn-sm" data-bs-toggle="modal" data-bs-target="#mxModal">
                    <img src="assets/feather/info.svg" alt="Info" width="32" height="16">
                </button>
            </th>
            <?php
            echo '</tr>';
            echo '</thead>';
            echo '<thead class="table-head-background table-head-smallfont">';
            echo '<tr>';
            echo '<th>Destination</th>';
            echo '<th>Prio</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            if (empty($mx)) {
                echo '<tr>';
                echo "<th>-</th>";
                echo '<th></th>';
                echo '</tr>';
            } else {
                foreach ($mx as $mx_entry) {
                    $priority = $mx_entry['priority'];
                    $destination = $mx_entry['destination'];
                    echo '<tr>';
                    echo "<th>" . $destination . "</th>";
                    echo '<th>' . $priority . '</th>';
                    echo '</tr>';
                }
            }
            echo '</tbody>';
            echo '</table>';

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

    function arrayToString($array) {
        $result = array();
        foreach ($array as $value) {
            if (is_array($value)) {
                $result[] = arrayToString($value);
            } else {
                $result[] = $value;
            }
        }
        return implode(', ', $result);
    }
?>
