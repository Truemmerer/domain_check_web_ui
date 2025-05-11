<?php
function myip() {
    echo '<div class="content-header">Your IP Adress</div>';

    // Display the IP address
    echo "Your IP address is: " . getClientIP();
}


// Function to get the client IP address
function getClientIP() {

    if (isset($_SERVER['HTTP_CLIENT_IP']))
        return $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        return $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        return $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        return $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        return $_SERVER['REMOTE_ADDR'];
    else
        return 'UNKNOWN';
}

?>
