<?php
    session_start();

    // Check if request has valid token
//    if(!($token == $_SESSION['api_token'])) {
//        echo "Invalid Token";
//        http_response_code(401);
//        exit;
//    }

    print_r(scan_dir('../../locale'));
?>
