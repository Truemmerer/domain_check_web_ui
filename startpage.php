<?php


function show_startpage() {

?>
    <div class="center_welcome">
        <p align="center"><img src="./assets/window-domain_icon-icons.com_52810.png" width="256"></p>
        <h2> Domain Check - WebUI </h2>
    </div>

    <div class="credits">
        <h2>About</h2>
        This tool is intended to provide simple and functional help in checking domains. 
        <br>
        <br>    
        <h5>Already available features:</h5>
        - nslookup (Default: Authorative, Google, Cloudflare, OpenDNS)
        <br>
        - Configurable nameserver list
        <br>
        - Warning if different nameservers deliver different DNS.
        <br>
        - PTR/rDNS Check
        <br>
        - whois
        <br>
        - Whois summary so that information can be quickly tracked.
        <br>
        - Health Check (Status of a Domain, Nameserver, DNS Check, rDNS (IPv4) Check, PTR (IPv4) Check)
        <br>
        - Dark Mode (only 🤗)
        <br>
        <br>
        <br>
        <h5>ToDo:</h5>
        - DNSSEC
        <br>
        - dig
        <br>
        - PTR/rDNS IPv6
        <br>
        - Support for more tld in Quick Informations by Whois
        <br>
        - Add warning messages by bad Domain Status
    </div>

<?php 
    }
?>
