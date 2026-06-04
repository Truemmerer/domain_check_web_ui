<?php

    /**
     * Sender Policy Framework (SPF) check
     * @param $toproof - Domain
     */

    require_once 'src/helper.php';
    require_once 'src/dnscheck.php';

    function spf_check($toproof) {

        if (!is_safe_domain($toproof)) {
            echo '<div class="alert alert-warning"><strong>Note!</strong> Invalid domain.</div>';
            return;
        }

        // Get TXT Records — use the same hardened helper as dns_check.
        // We resolve the authoritative nameserver first so we look at the
        // same data print_dns_check would.
        $tld = get_tld($toproof);
        $nameservers = ($tld !== "none") ? get_authorative_nameservers($toproof, $tld) : [];
        $nameserver_ip = false;
        if (!empty($nameservers)) {
            $first_nameserver = $nameservers[0];
            if (isset($first_nameserver['ns_ip']) && is_safe_ip($first_nameserver['ns_ip'])) {
                $nameserver_ip = $first_nameserver['ns_ip'];
            }
        }

        // If we have an authoritative NS we query it; otherwise fall back
        // to a local recursive lookup via a public resolver.
        $txt_records = [];
        if ($nameserver_ip !== false) {
            $txt_records = get_txt_records($toproof, $nameserver_ip);
        } else {
            // Fallback via a known-good resolver (validated as IP literal)
            $txt_records = get_txt_records($toproof, '1.1.1.1');
        }

        $spf_records = [];

        echo '<div class="content-header">The following SPF records were found</div>';
        foreach ($txt_records as $txt_record) {
            // remove surrounding quotes from string
            $txt_record = trim($txt_record, '"');

            // Check if string begins with 'v=spf1'
            if (preg_match('/^v=spf1/', $txt_record)) {
                $spf_records[] = $txt_record;
            }
        }

        if (empty($spf_records)) {
            echo "No SPF Record found";
            return;
        }

        foreach ($spf_records as $spf_record) {
            $parts = explode(' ', $spf_record);

            echo h($spf_record);
            echo '<table>';

            foreach ($parts as $part) {
                echo '<tr>';
                if (strpos($part, 'v=') === 0) {
                    echo '<td>SPF Version:</td>';
                    echo '<td>' . h(substr($part, 2)) . '</td>';
                } elseif ($part === 'a' || strpos($part, 'a:') === 0 || strpos($part, 'a/') === 0) {
                    echo '<td><br/>Mechanism:</td>';
                    echo '<td><br/>a (Allow mail from the domain\'s A records):</td>';
                    if ($nameserver_ip !== false) {
                        $ipv4_entries = get_a_records($toproof, $nameserver_ip);
                        if (!empty($ipv4_entries)) {
                            foreach ($ipv4_entries as $entry) {
                                echo '<tr><td></td><td>' . h($entry) . '</td></tr>';
                            }
                        } else {
                            echo '<tr><td></td><td>No IPv4 entries found</td></tr>';
                        }
                    }
                } elseif ($part === 'mx' || strpos($part, 'mx:') === 0 || strpos($part, 'mx/') === 0) {
                    echo '<td><br/>Mechanism:</td>';
                    echo '<td><br/>mx (Allow mail from the domain\'s MX records):</td>';
                    if ($nameserver_ip !== false) {
                        $mx_entries = get_mx_records($toproof, $nameserver_ip);
                        if (!empty($mx_entries)) {
                            foreach ($mx_entries as $entry) {
                                echo '<tr><td></td><td>' . h($entry['destination']) . '</td></tr>';
                            }
                        } else {
                            echo '<tr><td></td><td>No MX entries found</td></tr>';
                        }
                    }
                } elseif (strpos($part, 'ip4:') === 0) {
                    echo '<td><br/>Mechanism:</td>';
                    echo '<td><br/>ip4 (Allow mail from the specified IPv4 address)</td>';
                    echo '<tr><td></td><td>' . h(substr($part, 4)) . '</td>';
                } elseif (strpos($part, 'ip6:') === 0) {
                    echo '<td><br/>Mechanism:</td>';
                    echo '<td><br/>ip6 (Allow mail from the specified IPv6 address)</td>';
                    echo '<tr><td></td><td>' . h(substr($part, 4)) . '</td>';
                } elseif (strpos($part, 'ptr') === 0) {
                    echo '<td><br/>Mechanism:</td>';
                    echo '<td><br/>ptr (Matches if the DNS reverse-lookup for the IP address matches the given domain)</td>';
                    echo '<tr><td></td><td>' . h(substr($part, 4)) . '</td>';
                } elseif (strpos($part, 'include:') === 0) {
                    echo '<td><br/>Mechanism:</td>';
                    echo '<td><br/>include (Include the SPF record from the specified domain)</td>';
                    echo '<tr><td></td><td>' . h(substr($part, 8)) . '</td>';
                } elseif (strpos($part, '-all') === 0) {
                    echo '<td><br/>Default Policy:</td>';
                    echo '<td><br/>-all (Reject all mail that doesn\'t match any of the above mechanisms)</td>';
                } elseif (strpos($part, '~all') === 0) {
                    echo '<td><br/>Default Policy:</td>';
                    echo '<td><br/>~all (soft fail: Also allow mails if they are not covered by the mechanisms mentioned above)</td>';
                } else {
                    echo '<td><br/>Unknown Mechanism:</td>';
                    echo '<td><br/>' . h($part) . '</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        }
    }
?>
