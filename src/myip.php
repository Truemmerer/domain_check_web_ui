<?php
require_once 'src/helper.php';

function myip() {
    echo '<div class="content-header">Your IP Adress</div>';
    // Display the IP address (escaped + validated)
    echo "Your IP address is: " . h(getClientIP());
}


/*
 * Return the connecting client's IP.
 *
 * SECURITY NOTE
 *  The original code blindly trusted HTTP_CLIENT_IP, HTTP_X_FORWARDED_FOR
 *  etc. These headers can be set by ANY client and therefore must NEVER
 *  be used unless the server is behind a known reverse proxy. We default
 *  to REMOTE_ADDR (the actual TCP peer). If the application is deployed
 *  behind a trusted proxy, the operator should set $TRUSTED_PROXIES below
 *  to that proxy's IP — only then will X-Forwarded-For be honoured.
 */
function getClientIP() {

    // Edit this list to include the IPs of your reverse proxies (e.g.
    // nginx, Cloudflare, Traefik). Anything coming from outside of these
    // IPs will have its forwarding headers ignored.
    $TRUSTED_PROXIES = [
        // '127.0.0.1',
        // '::1',
    ];

    $remote = $_SERVER['REMOTE_ADDR'] ?? '';

    if (!empty($TRUSTED_PROXIES) && in_array($remote, $TRUSTED_PROXIES, true)) {
        // Use the LEFT-MOST entry of X-Forwarded-For, which by convention
        // is the original client IP.
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $xff = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $candidate = trim($xff[0]);
            if (is_safe_ip($candidate)) {
                return $candidate;
            }
        }
        if (!empty($_SERVER['HTTP_X_REAL_IP']) && is_safe_ip(trim($_SERVER['HTTP_X_REAL_IP']))) {
            return trim($_SERVER['HTTP_X_REAL_IP']);
        }
    }

    if (is_safe_ip($remote)) {
        return $remote;
    }
    return 'UNKNOWN';
}
?>
