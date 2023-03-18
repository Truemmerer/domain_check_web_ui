<?php

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // Opendns 
    function opendns_check($toproof) {
        $domain = escapeshellarg($_GET["toproof"]);
        $command = "nslookup " . escapeshellcmd($domain) . " 208.67.222.222";
        $output = shell_exec($command);
        $opendns_ipv4_addresses = array();
        $opendns_ipv6_addresses = array();


        //-----------------------------------------------------------------------------------
        // IPv4 Records
        preg_match_all('/\b(?!\b208\.67\.222\.222\b)\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b/', $output, $matches_ipv4);
        foreach ($matches_ipv4[0] as $ip) {
            $opendns_ipv4_addresses[] = $ip;
        }


        //-----------------------------------------------------------------------------------
        // IPv6 Records
        preg_match_all('/\b(?!\b208\.67\.222\.222\b)(?:(?:[0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}|(?:[0-9a-fA-F]{1,4}:){6}(?::[0-9a-fA-F]{1,4}|:)|(?:[0-9a-fA-F]{1,4}:){5}(?:(?::[0-9a-fA-F]{1,4}){1,2}|:)|(?:[0-9a-fA-F]{1,4}:){4}(?:(?::[0-9a-fA-F]{1,4}){1,3}|(?::[0-9a-fA-F]{1,4})?:)|(?:[0-9a-fA-F]{1,4}:){3}(?:(?::[0-9a-fA-F]{1,4}){1,4}|(?::[0-9a-fA-F]{1,4}){0,2}:)|(?:[0-9a-fA-F]{1,4}:){2}(?:(?::[0-9a-fA-F]{1,4}){1,5}|(?::[0-9a-fA-F]{1,4}){0,3}:)|(?:[0-9a-fA-F]{1,4}:){1}(?:(?::[0-9a-fA-F]{1,4}){1,6}|(?::[0-9a-fA-F]{1,4}){0,4}:)|(?::(?:(?::[0-9a-fA-F]{1,4}){1,7}|(?::[0-9a-fA-F]{1,4}){0,5}:)))(?<![:.])\b/', $output, $matches_ipv6);
        foreach ($matches_ipv6[0] as $ip) {
            $opendns_ipv6_addresses[] = $ip;
        }

        //-----------------------------------------------------------------------------------
        // TXT Records 

        $command_txt = "nslookup -q=txt " . escapeshellcmd($domain) . " 208.67.222.222";
        $output_txt = shell_exec($command_txt);
        $pattern = '/text = "([^"]+)"/';
        preg_match_all($pattern, $output_txt, $matches_txt);
        $opendns_txt_records = $matches_txt[1];

        //-----------------------------------------------------------------------------------
        // CNAME Records 

        $command_cname = "nslookup -q=CNAME " . escapeshellcmd($domain) . " 208.67.222.222";
        $output_cname = shell_exec($command_cname);
        $pattern_cname = '/canonical name = (.+)/';
        preg_match_all($pattern_cname, $output_cname, $matches_cname);
        $opendns_cname_records = $matches_cname[1];

        //-----------------------------------------------------------------------------------
        // MX Records
        $command_mx = "nslookup -q=MX " . escapeshellcmd($domain) . " 208.67.222.222";
        $output_mx = shell_exec($command_mx);
        $pattern = '/exchanger = (.+)/';
        preg_match_all($pattern, $output_mx, $matches_mx);
        $opendns_mx_records = $matches_mx[1];

        //-----------------------------------------------------------------------------------
        // NS Records
        $command_ns = "nslookup -q=ns " . escapeshellcmd($domain) . " 208.67.222.222";
        $output_ns = shell_exec($command_ns);
        $pattern = '/nameserver = (.+)/';
        preg_match_all($pattern, $output_ns, $matches_ns);
        $opendns_ns_records = $matches_ns[1];
        
        //---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        // END OF THE FILE
        return [$opendns_ipv4_addresses, $opendns_ipv6_addresses, $opendns_txt_records, $opendns_cname_records, $opendns_mx_records, $opendns_ns_records];


    }
?>