<?php

function dig($toproof, $typeToCheck) {

    $domain = ($toproof);

    $whatisit = whatisit($domain);
    if ($whatisit === 2) {        
        
        // Check if Domain is in IDN
        if ( is_idn($domain) ) {
            $domain = idn_to_puny($domain);
        }

        // Check nameserver in nameserver.yaml
        $authorative_nameserver_array = dig_authorative_nameserver($domain, $typeToCheck);
        $custom_nameserver_array = dig_custom_nameserver($domain, $typeToCheck);
            
        // Build output
        build_dig($authorative_nameserver_array, $custom_nameserver_array);
    } else {     
        ?>
        <div class="alert alert-info">
            <strong>Note:</strong> nslookup works only with a domain</a>.
        </div>
        <?php
    }
}

function build_dig($authorative_nameserver_array, $custom_nameserver_array) {
    
    // Output from authorative nameserver
    ?>
        <br>
        <br>
        <div class="alert card-box-nslookup-separator">
            <strong>Info:</strong> The following nameservers are the authoritative name servers:
        </div>
    <?php 

    foreach ($authorative_nameserver_array as $result) {

        $ns_ip = $result['ns_ip'];
        $ns_name = $result['ns_name'];
        $ns_output = $result['ns_output'];

        $collapseId = 'collapse' . ucfirst(str_replace(['_', '.'], '', $ns_name));

        echo '<div class="card card-box-style">';
        echo '<div class="card-header" role="button" data-bs-toggle="collapse" data-bs-target="#' . $collapseId . '" aria-expanded="false" aria-controls="' . $collapseId . '">';
        echo '<h2 class="mb-0">' . $ns_name . ' (' . $ns_ip . ')</h2>';
        echo '</div>';
        echo '<div class="collapse" id="' . $collapseId . '">';
        echo '<div class="card-body card-body-style">';
        echo '<div class="container-fluid">';
        echo '<pre>' . $ns_output . '</pre>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        
    }

    // Output nameserver from nameserver.yaml
    ?>
        <br>
        <br>
        <div class="alert card-box-nslookup-separator">
            <strong>Info:</strong> The following nameservers were also checked:
        </div>
    <?php
    foreach ($custom_nameserver_array as $result) {

        $ns_ip = $result['ns_ip'];
        $ns_name = $result['ns_name'];
        $ns_output = $result['ns_output'];

        $collapseId = 'collapse' . ucfirst(str_replace(['_', '.'], '', $ns_name));

        echo '<div class="card card-box-style">';
        echo '<div class="card-header" role="button" data-bs-toggle="collapse" data-bs-target="#' . $collapseId . '" aria-expanded="false" aria-controls="' . $collapseId . '">';
        echo '<h2 class="mb-0">' . $ns_name . ' (' . $ns_ip . ')</h2>';
        echo '</div>';
        echo '<div class="collapse" id="' . $collapseId . '">';
        echo '<div class="card-body card-body-style">';
        echo '<div class="container-fluid">';
        echo '<pre>' . $ns_output . '</pre>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        
    }

    
}

// Check nameserver in nameserver.yaml
function dig_custom_nameserver($domain, $typeToCheck) {

    // Import the nameserverlist.yaml as array
    $yamlContent = file_get_contents("nameserverlist.yaml");
    $nameserverlist = yaml_parse($yamlContent);
    $custom_nameserver_array = array();

    // Create Output for each Nameserver in nameserverlist.yaml
    foreach ($nameserverlist as $nameserver => $ns_ip) {

        $command = "dig $typeToCheck " . escapeshellcmd($domain) . " @$ns_ip";
        $output = shell_exec($command);
        $custom_nameserver_array[] = [
            'ns_ip' => $ns_ip,
            'ns_name' => $nameserver,
            'ns_output' => $output,
        ];

    } // End foreach

    return $custom_nameserver_array;

}

function dig_authorative_nameserver($domain, $typeToCheck) {
    // Check authorative nameservers
    $nameserver_array = get_nameserver_ip($domain);
    $authorative_nameserver_array = array();


    // Create Output for each Nameserver from $nameserver_array
    foreach ($nameserver_array as $nameserver) {

        $authoritative_ip = $nameserver['ns_ip'];
        $authoritative_name = $nameserver['ns_name'];

        $command = "dig $typeToCheck " . escapeshellcmd($domain) . " @$authoritative_ip";
        $output = shell_exec($command);
        $authorative_nameserver_array[] = [
            'ns_ip' => $authoritative_ip,
            'ns_name' => $authoritative_name,
            'ns_output' => $output,
        ];

    } // End foreach

     return $authorative_nameserver_array;

}

?>