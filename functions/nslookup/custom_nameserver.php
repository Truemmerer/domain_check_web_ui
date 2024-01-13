<?php

        //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // Check DNS of nameservers
    // You can modify the nameservers in the file nameserverlist.php 
    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    function nameserver_check($toproof) {
       
        // Import the nameserverlist.yaml as array
        $yamlContent = file_get_contents("nameserverlist.yaml");
        $nameserverlist = yaml_parse($yamlContent);
        
       
        $domain = escapeshellarg($toproof);
        // Find DNS Records for each Authoritative Nameserver

        $dns_array_result = [];

        foreach ($nameserverlist as $nameserver => $ns_ip) {

            $ipv4_addresses = [];
            $ipv6_addresses = [];
            $txt_records = [];
            $cname_records = [];
            $mx_records = [];
            $ns_records = [];

            // command for IPv4 and IPv6 Check
            $command = "nslookup " . escapeshellcmd($domain) . " $ns_ip";
            $output = shell_exec($command);
            
            //-----------------------------------------------------------------------------------
            // IPv4 Records


            preg_match_all('/\b(?!\b' . preg_quote($ns_ip, '/') . '\b)\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b/', $output, $matches_ipv4);
            foreach ($matches_ipv4[0] as $ip) {
                $ipv4_addresses[] = $ip;
            }


            //-----------------------------------------------------------------------------------
            // IPv6 Records
            $pattern_ipv6 = '/\b(?!\b' . preg_quote($ns_ip, '/') . '\b)(?:(?:[0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}|(?:[0-9a-fA-F]{1,4}:){6}(?::[0-9a-fA-F]{1,4}|:)|(?:[0-9a-fA-F]{1,4}:){5}(?:(?::[0-9a-fA-F]{1,4}){1,2}|:)|(?:[0-9a-fA-F]{1,4}:){4}(?:(?::[0-9a-fA-F]{1,4}){1,3}|(?::[0-9a-fA-F]{1,4})?:)|(?:[0-9a-fA-F]{1,4}:){3}(?:(?::[0-9a-fA-F]{1,4}){1,4}|(?::[0-9a-fA-F]{1,4}){0,2}:)|(?:[0-9a-fA-F]{1,4}:){2}(?:(?::[0-9a-fA-F]{1,4}){1,5}|(?::[0-9a-fA-F]{1,4}){0,3}:)|(?:[0-9a-fA-F]{1,4}:){1}(?:(?::[0-9a-fA-F]{1,4}){1,6}|(?::[0-9a-fA-F]{1,4}){0,4}:)|(?::(?:(?::[0-9a-fA-F]{1,4}){1,7}|(?::[0-9a-fA-F]{1,4}){0,5}:)))(?<![:.])\b/';
            preg_match_all($pattern_ipv6, $output, $matches_ipv6);
            foreach ($matches_ipv6[0] as $ip) {
                $ipv6_addresses[] = $ip;
            }

            //-----------------------------------------------------------------------------------
            // TXT Records 

            $command_txt = "nslookup -q=txt " . escapeshellcmd($domain) . " $ns_ip";
            $output_txt = shell_exec($command_txt);
            $pattern = '/text = "([^"]+)"/';
            preg_match_all($pattern, $output_txt, $matches_txt);
            $txt_records = $matches_txt[1];

            //-----------------------------------------------------------------------------------
            // CNAME Records 

            $command_cname = "nslookup -q=CNAME " . escapeshellcmd($domain) . " $ns_ip";
            $output_cname = shell_exec($command_cname);
            $pattern_cname = '/canonical name = (.+)/';
            preg_match_all($pattern_cname, $output_cname, $matches_cname);
            $cname_records = $matches_cname[1];

            //-----------------------------------------------------------------------------------
            // MX Records
            $command_mx = "nslookup -q=MX " . escapeshellcmd($domain) . " $ns_ip";
            $output_mx = shell_exec($command_mx);
            $pattern = '/exchanger = (.+)/';
            preg_match_all($pattern, $output_mx, $matches_mx);
            $mx_records = $matches_mx[1];

            //-----------------------------------------------------------------------------------
            // NS Records
            $command_ns = "nslookup -q=ns " . escapeshellcmd($domain) . " $ns_ip";
            $output_ns = shell_exec($command_ns);
            $pattern = '/nameserver = (.+)/';
            preg_match_all($pattern, $output_ns, $matches_ns);
            $ns_records = $matches_ns[1];

            $dns_array_result[] = [
                'ns_ip' => $ns_ip,
                'ns_name' => $nameserver,
                'ipv4' => $ipv4_addresses,
                'ipv6' => $ipv6_addresses,
                'txt' => $txt_records,
                'cname' => $cname_records,
                'mx' => $mx_records,
                'ns' => $ns_records,
            ];

        } // END of foreach

        return $dns_array_result;

    }


?>