<!-- Check what is to proof
---------------------------------
<?php

    // 0 = check failed
    // 1 = IP
    // 2 = Domain
    // 3 = URL  -- not used
    // 4 = E-Mail-Adress -- not used

    function whatisit($toproof) {
        if (filter_var($toproof, FILTER_VALIDATE_IP)) {
            return 1;
        } elseif (filter_var($toproof, FILTER_VALIDATE_DOMAIN)) {
            return 2;
        } else {
            ?>
                <div class="alert alert-info">
                    <strong>Note!</strong> You must enter a domain or IP.</a>.
                </div>
            <?php

            return 0;
        }
    }

?>

<!-- Extract Domain from a Subdomain
---------------------------------

<?php 
    function extractDomain($subdomain) {

        // Split the domain by periods
        $parts = explode('.', $subdomain);

        // Check if the domain has enough parts
        if (count($parts) > 2) {
            // Extract the last two parts as the main domain
            $mainDomain = $parts[count($parts) - 2] . '.' . $parts[count($parts) - 1];
            $cleanDomain = rtrim($mainDomain, "'");
        return $cleanDomain;
    }

        // Return the original domain if it doesn't have enough parts
        return $subdomain;
        
    }
?>