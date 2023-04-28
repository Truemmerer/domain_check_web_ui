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

    function ipv6_check($cloudflare_ipv6_addresses, $google_ipv6_addresses, $opendns_ipv6_addresses, $authoritative_ipv6_addresses) {

        if (empty(array_diff($cloudflare_ipv6_addresses, $google_ipv6_addresses, $opendns_ipv6_addresses, $authoritative_ipv6_addresses))) {
            // All the arrays have the same elements
            return true;
        } else {
            // The arrays have different elements
            return false;
        }
    }

    function txt_check($cloudflare_txt_records, $google_txt_records, $opendns_txt_records, $authoritative_txt_records) {

        if (empty(array_diff($cloudflare_txt_records, $google_txt_records, $opendns_txt_records, $authoritative_txt_records))) {
            // All the arrays have the same elements
            return true;
        } else {
            // The arrays have different elements
            return false;
        }
    }

    function cname_check($cloudflare_cname_records, $google_cname_records, $opendns_cname_records, $authoritative_cname_records) {

        if (empty(array_diff($cloudflare_cname_records, $google_cname_records, $opendns_cname_records, $authoritative_cname_records))) {
            // All the arrays have the same elements
            return true;
        } else {
            // The arrays have different elements
            return false;
        }
    }

    function mx_check($cloudflare_mx_records, $google_mx_records, $opendns_mx_records, $authoritative_mx_records) {

        if (empty(array_diff($cloudflare_mx_records, $google_mx_records, $opendns_mx_records, $authoritative_mx_records))) {
            // All the arrays have the same elements
            return true;
        } else {
            // The arrays have different elements
            return false;
        }
    }

    function ns_check($cloudflare_ns_records, $google_ns_records, $opendns_ns_records, $authoritative_ns_records) {

        if (empty(array_diff($cloudflare_ns_records, $google_ns_records, $opendns_ns_records, $authoritative_ns_records))) {
            // All the arrays have the same elements
            return true;
        } else {
            // The arrays have different elements
            return false;
        }
    }

    

?>