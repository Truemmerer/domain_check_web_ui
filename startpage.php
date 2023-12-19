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
        </br>       
        </br>       
        <h5>Already available features:</h5>
        <li>nslookup (Default: Authorative, Google, Cloudflare, OpenDNS)</li>
        <li>Configurable nameserver list</li>
        <li>Warning if different nameservers deliver different DNS.</li>
        <li>PTR/rDNS Check</li>
        <li>whois</li>
        <li>Whois summary so that information can be quickly tracked.</li>
        <li>Health Check (Status of a Domain, Nameserver, DNS Check, rDNS (IPv4) Check, PTR (IPv4) Check)</li>
        <li>IDN > Puny Converter</li>
        <li>Dark Mode (only 🤗)</li>
        </br>       
        </br>       
        <h5>ToDo:</h5>
        <li>DNSSEC</li>
        <li>PTR/rDNS IPv6</li>
        <li>Support for more tld in Quick Informations by Whois</li>
        <li>Add warning messages by bad Domain Status</li>

    </div>

<?php 
    }
?>
