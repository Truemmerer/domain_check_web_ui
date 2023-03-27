<?php

    function ipv4_check($cloudflare_ipv4_addresses, $google_ipv4_addresses, $opendns_ipv4_addresses, $authoritative_ipv4_addresses) {

        if (empty(array_diff($cloudflare_ipv4_addresses, $google_ipv4_addresses, $opendns_ipv4_addresses, $authoritative_ipv4_addresses))) {
            // All the arrays have the same elements
            return true;
        } else {
            // The arrays have different elements
            return false;
        }
    }

?>