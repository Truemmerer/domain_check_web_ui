<p align="center"><img src="./assets/window-domain_icon-icons.com_52810.png" width="256"></p>
<p align="center">A simple domain check tool</p>  

# Domain Check Web UI
### You can already use the tool at the following URL:
<a href="https://domcheck.org">domcheck.org</a>

<br/>

## Work in Progress 🎏
</br> 
This tool is intended to provide simple and functional help in checking domains. 

</br>

### Already available features:
- nslookup (Default: Authorative, Google, Cloudflare, OpenDNS)
- Configurable nameserver list
- Warning if different name servers deliver different DNS.
- PTR/rDNS Check
- whois
- Whois summary so that information can be quickly tracked.
- Health Check (Status of a Domain, Nameserver, DNS Check, rDNS (IPv4) Check, PTR (IPv4) Check)
- IDN > Puny Converter
- Dark Mode (only 🤗)  

</br>

### ToDo:
- DNSSEC
- dig
- Support for more domains for whois summary
- Warnings when the domain has a bad status.
- PTR/rDNS Check for IPv6

</br>

## Requirements
- You need to edit config.php
- bind-utils
- LAMP Server
- php-yaml
- php-intl

</br>

# Collaboration 👥

Let's make it better together! <br/> 
Feel free to open an issue if you have any problem or suggestions 🤍

</br>

## Contributors 🎎

<a href="https://github.com/Truemmerer/domain_check_web_ui/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=Truemmerer/domain_check_web_ui" />
</a>

Made with [contrib.rocks](https://contrib.rocks).

</br>

### Info
Icon from [Icon-Icons.com](https://icon-icons.com/icon/window-domain-www/52810) made by [Vecteezy](https://icon-icons.com/users/49oaZ80LDyqHrUI3wINLc/icon-sets/) licensed under <a rel="license" href="http://creativecommons.org/licenses/by/4.0/">Creative Commons Attribution 4.0 International License</a>.

The page uses the Bootstrap framework.
The integration is implemented locally. 
Bootsrap uses the <a rel="license" href="https://raw.githubusercontent.com/twbs/bootstrap/main/LICENSE">MIT license</a>.
