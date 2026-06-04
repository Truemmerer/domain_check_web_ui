<?php

    /**
     * ============================================================
     *  SECURITY / VALIDATION HELPERS
     * ============================================================
     *  These functions are the single source of truth for deciding
     *  whether a user-supplied value is a safe domain / IP / nameserver
     *  before it is handed to shell commands or echoed to HTML.
     *
     *  Rules of thumb:
     *   - NEVER pass user input directly to shell_exec, even with
     *     escapeshellcmd / escapeshellarg, without first whitelisting
     *     the *characters* allowed. escapeshellarg quotes the value
     *     but does NOT prevent options being interpreted by some
     *     tools (e.g. `whois -h ...`, `dig -f ...`) when the value
     *     starts with '-' or contains weird tokens.
     *   - NEVER echo user input to HTML without htmlspecialchars.
     * ============================================================
     */

    /**
     * Strict domain / hostname validation.
     * Accepts: labels of [a-z0-9-], dot-separated, ≤253 chars total,
     *          ≤63 chars per label, no leading/trailing hyphen per label,
     *          must contain at least one dot, must not start or end with a dot.
     * Returns: true / false.
     */
    function is_safe_domain($value) {
        if (!is_string($value)) return false;
        if (strlen($value) < 1 || strlen($value) > 253) return false;
        if ($value[0] === '-' || substr($value, -1) === '-') return false;
        // Allow only ascii letters, digits, dot, hyphen (puny / ascii form).
        // IDN must be converted to ascii (xn--...) BEFORE reaching the shell.
        if (!preg_match('/^[a-zA-Z0-9.\-]+$/', $value)) return false;
        if (strpos($value, '..') !== false) return false;
        if ($value[0] === '.' || substr($value, -1) === '.') return false;
        // Each label must be 1..63 chars and not start/end with hyphen.
        $labels = explode('.', $value);
        if (count($labels) < 2) return false;
        foreach ($labels as $label) {
            if (strlen($label) < 1 || strlen($label) > 63) return false;
            if ($label[0] === '-' || substr($label, -1) === '-') return false;
        }
        return true;
    }

    /**
     * Strict IPv4 / IPv6 validation. Returns true / false.
     */
    function is_safe_ip($value) {
        if (!is_string($value)) return false;
        return (bool) filter_var($value, FILTER_VALIDATE_IP);
    }

    /**
     * Accepts safe domain OR safe IP. Used wherever a value is going
     * to be passed to `dig`, `whois`, etc.
     */
    function is_safe_host($value) {
        return is_safe_domain($value) || is_safe_ip($value);
    }

    /**
     * E-mail validation (only used by `whatisit`).
     */
    function is_safe_email($value) {
        if (!is_string($value)) return false;
        if (strlen($value) > 254) return false;
        return (bool) filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Safe HTML output of any user / external string.
     */
    function h($value) {
        if ($value === null) return '';
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }

    /**
     * Hardened shell-argument quoting. Use INSTEAD OF escapeshellarg
     * when feeding a value to a tool that may interpret leading-dash
     * tokens as options (whois, dig, curl, ...).
     *
     * The caller MUST validate the value first (is_safe_host etc.).
     * This function only adds belt-and-braces quoting.
     */
    function safe_shell_arg($value) {
        // Defensive: refuse anything outside the strict charset.
        if (!preg_match('/^[a-zA-Z0-9.\-:]+$/', (string)$value)) {
            return "''";
        }
        return escapeshellarg((string)$value);
    }

    /**
     * Normalise a domain-shaped input the way DNS does:
     *   - DNS is case-insensitive (RFC 4343) -> lowercase
     *   - the fully-qualified form with a trailing dot is equivalent
     *     to the form without it -> drop the trailing dot
     * IPs and e-mails are returned unchanged (the local part of an
     * e-mail is case-sensitive per RFC 5321, even if rarely treated
     * that way in practice).
     */
    function normalize_input($value) {
        if (!is_string($value)) return $value;
        $v = trim($value);
        // If it walks like an IP, leave it alone.
        if (filter_var($v, FILTER_VALIDATE_IP)) {
            return $v;
        }
        // If it has an '@' we treat it as an e-mail and only lowercase
        // the domain part.
        $at = strrpos($v, '@');
        if ($at !== false) {
            $local  = substr($v, 0, $at);
            $domain = substr($v, $at + 1);
            $domain = rtrim($domain, '.');
            $domain = strtolower($domain);
            return $local . '@' . $domain;
        }
        // Treat as hostname/domain
        $v = rtrim($v, '.');
        $v = strtolower($v);
        return $v;
    }

    /** Check if $toproof is a domain, ip, url, e-mail-adress */
    function whatisit($toproof) {
        if (is_safe_ip($toproof)) {
            return "IP";
        } elseif (is_safe_email($toproof)) {
            return "E-Mail-Adress";
        } elseif (is_safe_domain($toproof)) {
            return "Domain";
        } else {
            return "none";
        }
    }

    /** Get tld from a domain */
    function get_tld($toproof) {
        // Reject anything that isn't a valid domain BEFORE touching the filesystem
        if (!is_safe_domain($toproof)) {
            return "none";
        }

        $tld_file = __DIR__ . "/tldlist.txt";
        if (!is_readable($tld_file)) {
            return "none";
        }
        $tld_array = file($tld_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($tld_array === false) {
            return "none";
        }

        $dot_count = substr_count($toproof, ".");
        $is_tld = false;

        /* Check whether the domain is a three-level domain. */
        if ($dot_count > 2) {
            $tld = preg_replace('/^.*\.(?=.*\..*\..*$)/', '', $toproof);
            $tld = "." . $tld;
            if (in_array($tld, $tld_array, true)) {
                return $tld;
            }
        }

        /* Check whether the domain is a two-level domain. */
        if ($dot_count > 1) {
            $tld = preg_replace('/^.*\.(?=.*\..*$)/', '', $toproof);
            $tld = "." . $tld;
            if (in_array($tld, $tld_array, true)) {
                return $tld;
            }
        }

        /* Check whether the domain is a one-level domain. */
        $tld = preg_replace('/^.*\.(?=.*\.*$)/', '', $toproof);
        $tld = "." . $tld;
        if (in_array($tld, $tld_array, true)) {
            return $tld;
        }

        return "none";
    }

    /*
        Removes a leading dot from a TLD string.
        Example: .com -> com
    */
    function get_string_after_last_dot($string) {
        $string = (string)$string;
        $last_dot_position = strrpos($string, '.');
        if ($last_dot_position !== false) {
            return substr($string, $last_dot_position + 1);
        }
        return $string;
    }

    /*
        Removes the trailing dot if present.
        Example: example.tld. -> example.tld
    */
    function remove_dot_if_last_char($string) {
        $string = (string)$string;
        if ($string !== '' && substr($string, -1) === '.') {
            return substr($string, 0, -1);
        }
        return $string;
    }

    /** Get registrable domain from a subdomain */
    function get_domain($subdomain, $tld) {

        // Ensure the TLD starts with a dot
        if ($tld === null || $tld === '' || $tld === 'none') {
            return $subdomain;
        }
        if ($tld[0] !== '.') {
            $tld = '.' . $tld;
        }

        // Remove the $tld from the end of the $subdomain
        if (substr($subdomain, -strlen($tld)) === $tld) {
            $domain_without_tld = substr($subdomain, 0, -strlen($tld));
        } else {
            $domain_without_tld = $subdomain;
        }

        // Remove subdomain part
        $parts = explode('.', $subdomain);

        if (count($parts) >= 2) {
            $secondLevelDomain = $parts[count($parts) - 2];
            return $secondLevelDomain . $tld;
        }

        return $subdomain;
    }

    /** Get Authorative Nameservers */
    function get_authorative_nameservers($toproof, $tld) {

        // Strict validation first
        if (!is_safe_domain($toproof)) {
            return [];
        }

        /* Get Domain if input is a subdomain */
        $toproof = get_domain($toproof, $tld);

        if (!is_safe_domain($toproof)) {
            return [];
        }

        /* Get Nameservers */
        $command = "dig +short NS " . safe_shell_arg($toproof) . " | sort -u";
        $nameservers = shell_exec($command);
        if ($nameservers === null) {
            return [];
        }
        $nameserver_list = explode("\n", $nameservers);
        $nameserver_array = [];

        foreach ($nameserver_list as $nameserver) {
            $nameserver = trim($nameserver);
            if ($nameserver === '') continue;

            // dig output may have a trailing dot — strip it for hostname check
            $ns_clean = remove_dot_if_last_char($nameserver);

            // Only accept syntactically valid hostnames returned by dig.
            // This protects gethostbyname() and any later shell use.
            if (!is_safe_domain($ns_clean)) continue;

            $ip = gethostbyname($ns_clean);
            // gethostbyname returns the input unchanged on failure
            if ($ip === $ns_clean || !is_safe_ip($ip)) {
                continue;
            }

            $nameserver_array[] = [
                'ns_name' => $ns_clean,
                'ns_ip'   => $ip,
                'type'    => 'authorative'
            ];
        }

        return $nameserver_array;
    }

    /**
     * Lightweight fallback YAML parser used when the optional `yaml`
     * PHP extension is unavailable. Handles only the trivial subset
     * needed here: `key: value` per line, with comments (#) and quoted
     * strings. Returns array on success, null on failure.
     */
    function simple_yaml_parse($yaml) {
        if (!is_string($yaml)) return null;
        $out = [];
        foreach (preg_split('/\r\n|\n|\r/', $yaml) as $line) {
            // Skip blank lines & full-line comments
            $trim = ltrim($line);
            if ($trim === '' || $trim[0] === '#') continue;
            // Ignore indented lines (this parser is flat key:value only)
            if ($trim !== $line) continue;
            // Strip trailing inline comments (but not inside quotes -
            // good enough for our tiny inputs)
            if (preg_match('/^([^:#]+):\s*(.*)$/', $line, $m)) {
                $k = trim($m[1]);
                $v = trim($m[2]);
                // Strip surrounding single or double quotes
                if (strlen($v) >= 2 && (
                    ($v[0] === '"'  && substr($v, -1) === '"') ||
                    ($v[0] === "'"  && substr($v, -1) === "'")
                )) {
                    $v = substr($v, 1, -1);
                }
                if ($k !== '') {
                    $out[$k] = $v;
                }
            }
        }
        return $out;
    }

    /**
     * Wrapper that prefers the proper yaml extension when present,
     * and falls back to the tiny parser above otherwise. Returns
     * array or null.
     */
    function parse_yaml_file_safe($path) {
        if (!is_readable($path)) return null;
        $content = file_get_contents($path);
        if ($content === false) return null;
        if (function_exists('yaml_parse')) {
            $parsed = @yaml_parse($content);
            if (is_array($parsed)) return $parsed;
        }
        return simple_yaml_parse($content);
    }

?>
