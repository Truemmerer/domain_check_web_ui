<?php
    require_once 'src/helper.php';

    function geo($toproof, $type) {

        echo '<div class="content-header">Following locations are found</div>';

        // print location of domain ip-adresses
        if ($type === 'Domain') {
            require_once 'src/dnscheck.php';

            if (!is_safe_domain($toproof)) {
                echo '<div class="alert alert-warning"><strong>Note!</strong> Invalid domain.</div>';
                return;
            }

            // get first authorative nameserver
            $tld = get_tld($toproof);
            if ($tld === "none") {
                echo '<div class="alert alert-warning"><strong>Note!</strong> TLD unknown.</div>';
                return;
            }

            $nameservers = get_authorative_nameservers($toproof, $tld);
            $nameserver_ip = false;
            if (!empty($nameservers)) {
                $first_nameserver = $nameservers[0];
                if (isset($first_nameserver['ns_ip']) && is_safe_ip($first_nameserver['ns_ip'])) {
                    $nameserver_ip = $first_nameserver['ns_ip'];
                }
            }

            if ($nameserver_ip === false) {
                echo '<div class="alert alert-warning"><strong>Note!</strong> No authoritative nameserver found.</div>';
                return;
            }

            // get ipv4 and ipv6 records
            $a_records    = get_a_records($toproof, $nameserver_ip);
            $aaaa_records = get_aaaa_records($toproof, $nameserver_ip);

            if (!empty($a_records)) {
                foreach ($a_records as $record) {
                    if (is_safe_ip($record)) get_geo_data($record);
                }
            }
            if (!empty($aaaa_records)) {
                foreach ($aaaa_records as $record) {
                    if (is_safe_ip($record)) get_geo_data($record);
                }
            }

        } elseif ($type === 'IP') {

            if (!is_safe_ip($toproof)) {
                echo '<div class="alert alert-warning"><strong>Note!</strong> Invalid IP.</div>';
                return;
            }
            get_geo_data($toproof);

        } else {
            echo 'Not supported search';
        }

        echo '<br/> -------- <br/> The ripe.net api with maxmind-geo-lite was used';
    }

    function get_geo_data($ipAddress) {

        if (!is_safe_ip($ipAddress)) {
            return;
        }

        // Strictly construct the URL with urlencode on the already-validated IP.
        // Note: file_get_contents follows redirects by default — we limit
        // that with a stream context and a short timeout. The URL host is
        // a fixed string, so SSRF via host substitution is not possible.
        $url = "https://stat.ripe.net/data/maxmind-geo-lite/data.json?resource=" . urlencode($ipAddress);

        $ctx = stream_context_create([
            'http' => [
                'timeout'        => 10,
                'follow_location' => 0,
                'ignore_errors'  => true,
                'header'         => "User-Agent: domain_check_web_ui\r\n",
            ],
            'https' => [
                'timeout'        => 10,
                'follow_location' => 0,
                'ignore_errors'  => true,
                'header'         => "User-Agent: domain_check_web_ui\r\n",
            ],
        ]);

        $response = @file_get_contents($url, false, $ctx);

        if ($response === false) {
            echo "no response";
            return;
        }

        // Decode the JSON response
        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo 'Error: Unable to decode JSON response';
            return;
        }

        // Extract and display the location data
        if (isset($data['data']['located_resources']) && is_array($data['data']['located_resources'])) {
            foreach ($data['data']['located_resources'] as $resource) {
                echo 'Resource: ' . h($resource['resource'] ?? 'N/A') . "<br>";

                if (isset($resource['locations']) && is_array($resource['locations'])) {
                    foreach ($resource['locations'] as $location) {

                        // null-coalesce BEFORE htmlspecialchars to avoid
                        // passing null/array to htmlspecialchars (the
                        // original code did the opposite).
                        $country   = h($location['country']   ?? 'N/A');
                        $city      = h($location['city']      ?? 'N/A');
                        $latitude  = h($location['latitude']  ?? 'N/A');
                        $longitude = h($location['longitude'] ?? 'N/A');

                        echo '<table>';
                            echo '<tr><td>Country:</td><td>'   . $country   . '</td></tr>';
                            echo '<tr><td>City:</td><td>'      . $city      . '</td></tr>';
                            echo '<tr><td>Latitude:</td><td>'  . $latitude  . '</td></tr>';
                            echo '<tr><td>Longitude:</td><td>' . $longitude . '</td></tr>';
                        echo '</table>';
                        echo '<br/><br/>';
                    }
                }
            }
        } else {
            echo "No location data found in the response.";
        }
    }
?>
