<?php
    function geo($toproof, $type) {

        echo '<div class="content-header">Following locations are found</div>';

        // print location of domain ip-adresses
        if ($type === 'Domain') {
            require_once 'src/dnscheck.php';
            require_once 'src/helper.php';

            // get first authorative nameserver 
            $tld = get_tld($toproof);
            $nameservers = get_authorative_nameservers($toproof, $tld);
            $nameserver_ip = false;
            if (!empty($nameservers)) {
                $first_nameserver = $nameservers[0];
                if (isset($first_nameserver['ns_ip'])) {
                    $nameserver_ip = $first_nameserver['ns_ip'];
                }
            }

            // get ipv4 and ipv6 records
            $a_records = get_a_records($toproof, $nameserver_ip);
            $aaaa_records = get_aaaa_records($toproof, $nameserver_ip);
            
            // print location of ipv4 adresses
            if (!empty($a_records)) {
                foreach ($a_records as $record) {
                    get_geo_data($record);
                }    
            }

            // print location from ipv6 adresses
            if (!empty($aaaa_records)) {
                foreach ($aaaa_records as $record) {
                    get_geo_data($record);
                }    
            }

        // print geolocation from a ip-adress
        } elseif ($type === 'IP') {

            get_geo_data($toproof);

        } else {
            echo 'Not supported search';
        }

        echo '</br> -------- </br> The ripe.net api with maxmind-geo-lite was used'; 

    }

    function get_geo_data($ipAddress) {

        $url = "https://stat.ripe.net/data/maxmind-geo-lite/data.json?resource=" . urlencode($ipAddress);
        $response = file_get_contents($url);

        if ($response === FALSE) {
            echo "no response";
        }

        // Decode the JSON response
        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            die('Error: Unable to decode JSON response');
        }

        // Extract and display the location data
        if (isset($data['data']['located_resources'])) {
            foreach ($data['data']['located_resources'] as $resource) {
                echo 'Resource: ' . htmlspecialchars($resource['resource']) . "<br>";

                if (isset($resource['locations'])) {
                    foreach ($resource['locations'] as $location) {
                        
                        $country = htmlspecialchars($location['country']) ?? 'N/A';
                        $city = htmlspecialchars($location['city']) ?? 'N/A';
                        $latitude = htmlspecialchars($location['latitude']) ?? 'N/A';
                        $longitude = htmlspecialchars($location['longitude']) ?? 'N/A';

                        echo '<table>';
                            echo '<tr>';
                                echo '<td> Country: </th>';
                                echo '<td>' . $country . '</th>';
                            echo '</tr>';
                            echo '<tr>';
                                echo '<td> City: </th>';
                                echo '<td>' . $city . '</th>';
                            echo '</tr>';
                            echo '<tr>';
                                echo '<td> Latitude: </th>';
                                echo '<td>' . $latitude . '</th>';
                            echo '</tr>';
                            echo '<tr>';
                                echo '<td> Longitude: </th>';
                                echo '<td>' . $longitude . '</th>';
                            echo '</tr>';
                        echo '</table>';
                        echo '</br>';
                        echo '</br>';
                    }
                }
            }
        } else {
            echo "No location data found in the response.";
        }
    }


?>