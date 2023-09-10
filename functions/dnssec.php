<?php

    function dnssec($toproof) {

        $dnskey = dnskey_get($toproof);
        $ds = ds_get($toproof);
        echo $dnskey;
        echo $ds;
        
    }

    function dnskey_get($toproof) {
        $dnskey = dns_get_record($domain, DNS_KEY);
        return $dnskey;
    }

    function ds_get($toproof) {
        $ds = dns_get_record($domain, DNS_DS);
        return $ds;
    }
?> 

