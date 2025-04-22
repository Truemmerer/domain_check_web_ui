<?php

// Note: You can edit the list of nameservers in the nameserverlist.yaml file.  

// Page title
$pageTitle = "Domain Check Web UI";

// This Domain is used if you click on the logo
$working_domain = "https://domain.tld";

// The path to your impressum
$impressum = "https://domain.tld";

// The path to your privacy policy
$privacy_policy = "https://domain.tld";

// The URL to the Github project. 
$contribute = "https://github.com/Truemmerer/domain_check_web_ui";

/**
 * Bootstrap Localy?
 * You can add bootstrap under ./bootstrap and turn this to true if you want to use bootstrap locally.
*/

$bootstrap_localy = false;

/**
 * OpenSearch allows users to use the page as a search directly in the URL bar in the web browser.
 * You can activate or deactivate individual search providers in the following case.
 * If you want to use it, you must configure the files under the folder opensearch. 
 * The following must be replaced in the files:
 * [NAME], [URL], [FAVICON_URL]
*/

$opensearch_enabled = false; // Activate OpenSearch

$dns_search = true;
$whois_search = true;
$rdns_search = true;
$punyconvert_search = true;
$spf_search = true;

/* -------------------------------
 * Enabled functions
 * set to false to disable
 * -------------------------------
 * dns_check = can't be disabled
 * whois = Whois
 * puny = IDN > Puny Converter
 * dnssec = Check DNSSEC Errors
 * rdns = rDNS and PTR Check
 * provider = Provider Check
 * spfcheck = SPF Check
*/

$whois = true;
$puny = true;
$dnssec = false;
$rdns = true;
$provider = false; // not implemented yet
$spfcheck = true;

?>
