<?php

    // 0 = check failed
    // 1 = IP
    // 2 = Domain
    // 3 = URL  -- not used
    // 4 = E-Mail-Adress -- not used

    function whatisit($toproof) {
        if (filter_var($toproof, FILTER_VALIDATE_IP)) {
            return 1;
        } elseif (filter_var($toproof, FILTER_VALIDATE_DOMAIN)) {
            return 2;
        } else {
            ?>
                <div class="alert alert-info">
                    <strong>Note!</strong> You must enter a domain or IP.</a>.
                </div>
            <?php

            return 0;
        }
    }

?>