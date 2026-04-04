<!--
This file is for cdns (Custom DNS check)
User can enter a custom Nameserver for the check
-->

<?php

    function cdns($toproof, $cnsa) {
        if ($cnsa === null) {
            ask_nameserver($toproof);
        } else {

            $dns_array = get_custom_nameserver_dns($toproof, $cnsa);            
            print_dns_check($dns_array);
        }
    }

    function ask_nameserver($toproof){
        echo '<div class="content-header">Custom DNS check</div>';
        ?>
        <h4 Enter nameserver:></h4>
        <form class="d-flex flex-wrap justify-content-center" action="index.php" method="get">
            <input type="text" class="form-control me-2 search-color" placeholder="Domain to proof" name="toproof" value="<?php echo $toproof; ?>">
            <input type="text" class="form-control me-2 search-color" placeholder="Nameserver Domain or IP" name="cnsa" value="">
            <div class="btn-group mt-2 w-100">
                <button type="submit" class="btn btn-success" style="width: 60%;" name="action" value="cdns">DNS Check</button>
            </div>
        </form>   
        <?php
    }

    function get_custom_nameserver_dns($toproof, $cnsa) {
            // get DNS records (A; AAAA; TXT; CNAME; MX)
            $ipv4_addresses = get_a_records($toproof, $cnsa);
            $ipv6_addresses = get_aaaa_records($toproof, $cnsa);
            $txt_adresses = get_txt_records($toproof, $cnsa);
            $cname_adresses = get_cname_records($toproof, $cnsa);
            $mx_addresses = get_mx_records($toproof, $cnsa);

            $dns_array_result[] = [
                'ns_ip' => $cnsa,
                'ns_name' => $cnsa,
                'type' => "custom",
                'ipv4' => $ipv4_addresses,
                'ipv6' => $ipv6_addresses,
                'txt' => $txt_adresses,
                'cname' => $cname_adresses,
                'mx' => $mx_addresses,
            ];

        return $dns_array_result;
    }
?>

