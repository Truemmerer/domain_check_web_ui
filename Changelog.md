
### 04.2025.1-dev
- Improvements for spf check
- Mobile style

### 04.2025.0-dev
- Buttons on the start page are now the same size as the search bar.
- Optimized display of the DNS check.

Whois reworked:
  - Quick information are removed (temporary) 
    The reason for this is that this has only worked for a few tlds so far.
    This should therefore be corrected and then reintegrated.
  - Collapse are removed. For a more clean look.
  - Whois now uses whois -h instead of whois to provide more accurate information.
    
### 10.2024.0-dev
This version contains major changes to the last version.
- Revised navigation bar. Optimized for smaller screens.
- nslookup and dig to DNS Check merged.
- DNS check is again the default search
- DNS-Check has been completely rewritten and some bugs have been fixed.
- Individual checks can be deactivated in config.php.
- Provider Check has been added. Currently only netcup webhostings are supported.
- Health Check has been removed.
- Pagetitle is now the title in the tab

### 03.2024.2
- Page title configuration added

### 03.2024.1
- nslookup: MX Records priority and destination separated
- nslookup: DNS type explanation has been added.
- dig: Support for the following DNS records added: CNAME, OPENPGPKEY, SSHFP

### 01.2024.2
- Add DNSSEC (Partial) 
- Add DNSSEC in Healthcheck

### 01.2024.1
- Error in nslookup fixed
- Fixed bug that idn domains did not work in dig

### 12.2023.1
- dig inplemented

### 09.2023.1
- Add IDN to Puny Converter as Standalone Tool.
- Add IDN to Puny Converter for nslookup, healthcheck, whois
- Add Changelog in footer

### 09.2023.0
- The name servers can now be customized. 
  nameserverlist.yaml
- Under nslookup, the domains and the IP addresses of the checked name servers are now displayed.
- Authoritative nameservers and custom nameservers have been visually separated under nslookup.
- A see what is newly added.
- Update already avaible featueres on startpage.
- Update ToDo on startpage.
- Add a Changelog
- Add new colors for: background, navbar, footer, dropdown menu, searchbar

### 07.2023.1
- Add Health Check Tool as new default

### 07.2023.0
- Bugfix for whois
- Bugfix for modal-content dark-Mode

### 04.2023.1
- New navbar that works better across devices.

### 04.2023.0
- Standalone rDNS/PTR check

### 03.2023.1
- Add Quick Informmations to whois

### 03.2023.0
- First release from own nslookup
- First release from own whois

### 01.2023.0
- First release