<?php
require_once 'src/helper.php';

// PUNYCONVERTER

// Check if Domain is in IDN or Puny and set IDN an Puny Variable
function punyconvert($toproof) {

    $idn = "";
    $puny = "";

    if (is_idn($toproof)) {
        $puny = idn_to_puny($toproof);
        $idn  = $toproof;
    } elseif (is_puny($toproof)) {
        $idn  = puny_to_idn($toproof);
        $puny = $toproof;
    } else {
        $idn  = $toproof;
        $puny = $toproof;
    }

    return [$idn, $puny];
}

// Print UI
function punyconvert_print($toproof) {
    list($idn, $puny) = punyconvert($toproof);
?>
    <h3>Punycode:</h3>
    <h4><span class="badge bg-success"><?php echo h($puny) ?></span></h4>
    <h3>IDN:</h3>
    <h4><span class="badge bg-success"><?php echo h($idn) ?></span></h4>
<?php
}

// convert IDN to Puny
function idn_to_puny($toproof) {
    $result = @idn_to_ascii((string)$toproof, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
    return $result === false ? $toproof : $result;
}

// convert Puny to IDN
function puny_to_idn($toproof) {
    $result = @idn_to_utf8((string)$toproof, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
    return $result === false ? $toproof : $result;
}

// Check if Domain is in IDN
function is_idn($domain) {
    $punycode = @idn_to_ascii((string)$domain, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
    if ($punycode === false) return false;
    return $domain !== $punycode;
}

// Check if Domain is in Puny
function is_puny($toproof) {
    $utf8 = @idn_to_utf8((string)$toproof, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
    if ($utf8 === false) return false;
    return $toproof !== $utf8;
}

?>
