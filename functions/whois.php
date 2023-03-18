<?php

function whois_output($toproof) {
    $domain = escapeshellarg($_GET["toproof"]);
    $command = "whois -q " . escapeshellcmd($domain);
    $output = shell_exec($command);
    $lines = explode("\n", $output);
    array_shift($lines);
    $output = implode("\n", $lines);
    echo nl2br(htmlspecialchars($output));
    return;
}

?>