<?php

    // get first NameServer IP
    function get_nameserver_ip($authoritative_ns) {
        $output = shell_exec("dig +short A $authoritative_ns");
        $ips = explode("\n", trim($output));
        return $ips[0]; // return the first IP address
    }


    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // Authoritative 
    function authoritative_check($toproof) {
        $domain = escapeshellarg($toproof);

        // FIND THE Nameserver

        $output_ns_detect = shell_exec("dig +short NS $domain");
         
        $nameservers = explode("\n", trim($output_ns_detect));
        $authoritative_ns = trim($nameservers[0]);
        $authoritative_ip = get_nameserver_ip($authoritative_ns);
                

        $command = "nslookup " . escapeshellcmd($domain) . " $authoritative_ip";
        $output = shell_exec($command);
        $authoritative_ipv4_addresses = array();
        $authoritative_ipv6_addresses = array();


        //-----------------------------------------------------------------------------------
        // IPv4 Records
        preg_match_all('/\b(?!\b' . preg_quote($authoritative_ip, '/') . '\b)\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b/', $output, $matches_ipv4);
        foreach ($matches_ipv4[0] as $ip) {
            $authoritative_ipv4_addresses[] = $ip;
        }


        //-----------------------------------------------------------------------------------
        // IPv6 Records
        $pattern_ipv6 = '/\b(?!\b' . preg_quote($authoritative_ip, '/') . '\b)(?:(?:[0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}|(?:[0-9a-fA-F]{1,4}:){6}(?::[0-9a-fA-F]{1,4}|:)|(?:[0-9a-fA-F]{1,4}:){5}(?:(?::[0-9a-fA-F]{1,4}){1,2}|:)|(?:[0-9a-fA-F]{1,4}:){4}(?:(?::[0-9a-fA-F]{1,4}){1,3}|(?::[0-9a-fA-F]{1,4})?:)|(?:[0-9a-fA-F]{1,4}:){3}(?:(?::[0-9a-fA-F]{1,4}){1,4}|(?::[0-9a-fA-F]{1,4}){0,2}:)|(?:[0-9a-fA-F]{1,4}:){2}(?:(?::[0-9a-fA-F]{1,4}){1,5}|(?::[0-9a-fA-F]{1,4}){0,3}:)|(?:[0-9a-fA-F]{1,4}:){1}(?:(?::[0-9a-fA-F]{1,4}){1,6}|(?::[0-9a-fA-F]{1,4}){0,4}:)|(?::(?:(?::[0-9a-fA-F]{1,4}){1,7}|(?::[0-9a-fA-F]{1,4}){0,5}:)))(?<![:.])\b/';
        preg_match_all($pattern_ipv6, $output, $matches_ipv6);
        foreach ($matches_ipv6[0] as $ip) {
            $authoritative_ipv6_addresses[] = $ip;
        }

        //-----------------------------------------------------------------------------------
        // TXT Records 

        $command_txt = "nslookup -q=txt " . escapeshellcmd($domain) . " $authoritative_ip";
        $output_txt = shell_exec($command_txt);
        $pattern = '/text = "([^"]+)"/';
        preg_match_all($pattern, $output_txt, $matches_txt);
        $authoritative_txt_records = $matches_txt[1];

        //-----------------------------------------------------------------------------------
        // CNAME Records 

        $command_cname = "nslookup -q=CNAME " . escapeshellcmd($domain) . " $authoritative_ip";
        $output_cname = shell_exec($command_cname);
        $pattern_cname = '/canonical name = (.+)/';
        preg_match_all($pattern_cname, $output_cname, $matches_cname);
        $authoritative_cname_records = $matches_cname[1];

        //-----------------------------------------------------------------------------------
        // MX Records
        $command_mx = "nslookup -q=MX " . escapeshellcmd($domain) . " $authoritative_ip";
        $output_mx = shell_exec($command_mx);
        $pattern = '/exchanger = (.+)/';
        preg_match_all($pattern, $output_mx, $matches_mx);
        $authoritative_mx_records = $matches_mx[1];

        //-----------------------------------------------------------------------------------
        // NS Records
        $command_ns = "nslookup -q=ns " . escapeshellcmd($domain) . " $authoritative_ip";
        $output_ns = shell_exec($command_ns);
        $pattern = '/nameserver = (.+)/';
        preg_match_all($pattern, $output_ns, $matches_ns);
        $authoritative_ns_records = $matches_ns[1];
        
        //---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        // END OF THE FILE
        return [$authoritative_ipv4_addresses, $authoritative_ipv6_addresses, $authoritative_txt_records, $authoritative_cname_records, $authoritative_mx_records, $authoritative_ns_records, $authoritative_ip];


    }


?>