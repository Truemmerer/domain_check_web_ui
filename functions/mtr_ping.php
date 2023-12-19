<?php

function mtr_check($toproof) {
    
    $maxSent = 10;
    $command = "/usr/sbin/mtr -r -c $maxSent $toproof";
    $output_mtr = shell_exec($command);
    if ($output_mtr === null) {
        echo "Error executing the MTR command.";
    } else {
        $formatted_mtr = nl2br($output_mtr);
        mtr_print($formatted_mtr);
    }
    $formated_mtr = nl2br($output_mtr);
    mtr_print($formated_mtr);

}


function mtr_print($formated_mtr) {

    ?>
    <!-- MTR --> 
    <div class="card card-box-style">
        <div class="card-header" role="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseOne">
        <a class="btn" data-bs-toggle="collapse" href="#collapseOne">
            My Traceroute (MTR)
        </a>
        </div>
        <div id="collapseOne" class="collapse show" data-bs-parent="#accordion">
            <div class="card-body card-body-style">
                <?php echo "<pre>$formated_mtr</pre>";  ?>
            </div>
        </div>
    </div>

    <?php


}

?>