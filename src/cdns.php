<?php
/*
 * Custom DNS check
 * User can enter a custom Nameserver for the check.
 *
 * SECURITY NOTES
 *  - $cnsa (custom nameserver) is a USER-controlled parameter and was
 *    previously passed directly to dig. It is now validated as IP or
 *    hostname by is_safe_host() in index.php BEFORE reaching this file.
 *    We also re-check here as defense in depth.
 */

    require_once 'src/helper.php';
    require_once 'src/dnscheck.php';

    function cdns($toproof, $cnsa) {
        if ($cnsa === null || $cnsa === '') {
            ask_nameserver($toproof);
            return;
        }

        // Defense in depth — even though index.php already validated.
        if (!is_safe_host($cnsa)) {
            echo '<div class="alert alert-warning"><strong>Note!</strong> The custom nameserver is not a valid hostname or IP.</div>';
            ask_nameserver($toproof);
            return;
        }
        if (!is_safe_domain($toproof)) {
            echo '<div class="alert alert-warning"><strong>Note!</strong> Invalid domain.</div>';
            return;
        }

        // If the custom NS was given as a hostname, resolve it to an IP
        // for the actual dig call (matches behaviour of authoritative
        // nameserver resolution elsewhere).
        $cnsa_ip = $cnsa;
        if (!is_safe_ip($cnsa_ip)) {
            $resolved = gethostbyname($cnsa);
            if ($resolved !== $cnsa && is_safe_ip($resolved)) {
                $cnsa_ip = $resolved;
            } else {
                echo '<div class="alert alert-warning"><strong>Note!</strong> Could not resolve the custom nameserver.</div>';
                return;
            }
        }

        $dns_array = get_custom_nameserver_dns($toproof, $cnsa, $cnsa_ip);
        print_dns_check($dns_array);
    }

    function ask_nameserver($toproof) {
        echo '<div class="content-header">Custom DNS check</div>';
        ?>
        <h4>Enter nameserver:</h4>
        <form class="d-flex flex-wrap justify-content-center" action="index.php" method="get">
            <input type="text" class="form-control me-2 search-color" placeholder="Domain to proof" name="toproof" value="<?php echo h($toproof); ?>">
            <input type="text" class="form-control me-2 search-color" placeholder="Nameserver Domain or IP" name="cnsa" value="">
            <div class="btn-group mt-2 w-100">
                <button type="submit" class="btn btn-success" style="width: 60%;" name="action" value="cdns">DNS Check</button>
            </div>
        </form>
        <?php
    }

    function get_custom_nameserver_dns($toproof, $cnsa_label, $cnsa_ip) {
            $ipv4_addresses = get_a_records($toproof, $cnsa_ip);
            $ipv6_addresses = get_aaaa_records($toproof, $cnsa_ip);
            $txt_adresses   = get_txt_records($toproof, $cnsa_ip);
            $cname_adresses = get_cname_records($toproof, $cnsa_ip);
            $mx_addresses   = get_mx_records($toproof, $cnsa_ip);

            $dns_array_result = [];
            $dns_array_result[] = [
                'ns_ip'   => $cnsa_ip,
                'ns_name' => $cnsa_label,
                'type'    => "custom",
                'ipv4'    => $ipv4_addresses,
                'ipv6'    => $ipv6_addresses,
                'txt'     => $txt_adresses,
                'cname'   => $cname_adresses,
                'mx'      => $mx_addresses,
            ];

        return $dns_array_result;
    }
?>
