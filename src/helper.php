<?php

    /** Check if $toproof is a domain, ip, url, e-mail-adress */

    function whatisit($toproof) {
        if (filter_var($toproof, FILTER_VALIDATE_IP)) {
            return "IP";
        } elseif (filter_var($toproof, FILTER_VALIDATE_DOMAIN)) {
            return "Domain";
        } elseif (filter_var($toproof, FILTER_VALIDATE_EMAIL)) {
            return "E-Mail-Adress";
        } elseif (filter_var($toproof, FILTER_VALIDATE_URL)) {
            return "URL";
        } else {
            return "none";
        }
    }

    /** Get tld from a domain */
    function get_tld($toproof) {
        $tld_array = file("src/tldlist.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        $dot_count = substr_count($toproof, ".");
        $is_tld = false;

        /* Check wheater the domain is a three-level domain. */
        if ($dot_count > 2) {
            $tld = preg_replace('/^.*\.(?=.*\..*\..*$)/', '', $toproof);
            $tld = "." . $tld;
            if (in_array($tld, $tld_array)) {
                $is_tld = true;
                return $tld;
            }
        }

        /* Check whether the domain is a two-level domain. */
        if ($dot_count > 1 && $is_tld === false) {
            $tld = preg_replace('/^.*\.(?=.*\..*$)/', '', $toproof);
            $tld = "." . $tld;

            if (in_array($tld, $tld_array)) {
                $is_tld = true;
                return $tld;
            }
        }

        /* Check whether the domain is a one-level domain. */           
        if ($is_tld === false) {
            $tld = preg_replace('/^.*\.(?=.*\.*$)/', '', $toproof);
            $tld = "." . $tld;
            if (in_array($tld, $tld_array)) {
                $is_tld = true;
                return $tld;
            }
        }

        // Get none back, if no tld is found
        if ($is_tld === false) {
            return "none";
        }
    }

    /*
        This function removes the first dot of a domain. 
        Example:
        .com -> com
    */
    function get_string_after_last_dot($string) {
        //check if the first character of $string is a dot
        $last_dot_position = strrpos($string, '.');

        // Check if dot was found
        if ($last_dot_position !== false) {
            // get the string behind the last dot
            return substr($string, $last_dot_position + 1);
        } else {
            // remove orginal string, if it did not begin with a dot
            return $string;
        }
    }

    /*
        This function removes a dot if it is the last char
        Example:
        example.tld. -> example.tld
    */
    function remove_dot_if_last_char($string) {
        // Check if the last character is a dot
        if (substr($string, -1) === '.') {
            // Remove the last dot
            return substr($string, 0, -1);
        }
        // Return the original string if it doesn't end with a dot
        return $string;
    }

    /** Get domain from a subdomain */
    function get_domain($subdomain, $tld) {

        // Ensure the TLD starts with a dot
        if ($tld[0] !== '.') {
            $tld = '.' . $tld;
        }

        // Remove the $tld from the end of the $subdomain
        if (substr($subdomain, -strlen($tld)) === $tld) {
            $domain_without_tld = substr($subdomain, 0, -strlen($tld));
        } else {
            $domain_without_tld = $subdomain;
        }

        // remove subdomain from $domain_without_tld
        $parts = explode('.', $subdomain);

        if (count($parts) >= 2) {
            $secondLevelDomain = $parts[count($parts) -2];

            // Add $tld again to $secondLevelDomain and return it
            return $secondLevelDomain . $tld; 

        }

    }

    /** Get Authorative Nameservers */
    function get_authorative_nameservers($toproof, $tld) {

        /* Get Domain if Domain is a subdomain */
        $toproof = get_domain($toproof, $tld);

        /* if domain is not valid, return none */
        if ($toproof === "domain not valid") {
            return "domain not valid";
        } else {

            /* Get Nameservers */
            $command = "dig +short NS " . escapeshellarg($toproof) . " | sort -u";
            $nameservers = shell_exec($command);
            $nameserver_list = explode("\n", $nameservers);
            $nameserver_array = [];

            // Convert Nameservers to array and add ns_ip
             foreach ($nameserver_list as $nameserver) {
                if (trim($nameserver) !== "") {
                    $nameserver_array[] = [
                        'ns_name' => $nameserver,
                        'ns_ip' => gethostbyname($nameserver),
                        'type' => 'authorative'
                    ];
                }
             }

            return $nameserver_array;
        }
    }

?>