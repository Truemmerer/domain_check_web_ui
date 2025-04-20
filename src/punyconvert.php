<?php

// PUNYCONVERTER

// Check if Domain is in IDN or Puny and set IDN an Puny Variable
function punyconvert($toproof) {
    
    $idn = "";
    $puny = "";

    if ( is_idn($toproof) ) {
       $puny = idn_to_puny($toproof);
       $idn = $toproof;
    } elseif ( is_puny($toproof) ) {
       $idn = puny_to_idn($toproof);
       $puny = $toproof;
    } elseif ( !is_puny($toproof) ) {
        $idn = $toproof;
        $puny = $toproof;
    }

    return [$idn, $puny];
}



// Print UI
function punyconvert_print($toproof) {
    list ($idn, $puny) = punyconvert($toproof);
?>
    <h3>Punycode:</h3>
    <h4><span class="badge bg-success"><?php echo $puny ?></span></h4>     
    <h3>IDN:</h3>
    <h4><span class="badge bg-success"><?php echo $idn ?></span></h4>     
<?php
}

// convert IDN to Puny
function idn_to_puny($toproof) {
    return idn_to_ascii($toproof);
}

// convert Puny to ID   
function puny_to_idn($toproof) {
    return idn_to_utf8($toproof);
}

// Check if Domain is in IDN
function is_idn($domain) {
    $punycode = idn_to_ascii($domain);
    return $domain !== $punycode;
}

// Check if Domain is in Puny
function is_puny($toproof) {
    $utf8 = idn_to_utf8($toproof);
    return $toproof !== $utf8;
}

?>