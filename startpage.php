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
                - nslookup (Authorative, Google, Cloudflare, OpenDNS)
                </br>
                - PTR/rDNS Check with IP by nslookup
                </br>
                - Warning if multiple DNS records are different on the checked name servers.
                </br>
                - whois
                </br>
                - Quick Informations by Whois
                </br>
                - Dark Mode (only 🤗)  
                </br>
                </br>
                </br>
                <h5>ToDo:</h5>
                - DNSSEC
                </br>
                - dig
                </br>
                - PTR/rDNS standalone
                </br>
                - Support for more tld in Quick Informations by Whois
                </br>
                - Add warning messages by bad Domain Status
            </div>

            <div class="footer">
                To contribute, visit <a href="https://github.com/Truemmerer/domain_check_web_ui/">Github Project</a>
                </br>
                </br>
                This work is licenced under a <a rel="license" href="./LICENSE">GNU GENERAL PUBLIC LICENSE</a>.
                </br>
                Icon from <a href="https://icon-icons.com/icon/window-domain-www/52810">Icon-Icons.com</a> made by <a href="https://icon-icons.com/users/49oaZ80LDyqHrUI3wINLc/icon-sets/">Vecteezy</a> licensed under <a rel="license" href="http://creativecommons.org/licenses/by/4.0/">Creative Commons Attribution 4.0 International License</a>.
                </br>                
            </div>
        </div>

<?php 
    }
?>
