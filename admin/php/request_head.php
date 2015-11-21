<?php
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');

    if(!empty($_POST['token'])) {
        $token = $_POST['token'];
    }

    if(!empty($_GET['token'])) {
        $token = $_GET['token'];
    }

    // Check if request has valid token
    if(!($token == $_SESSION['api_token'])) {
        echo "Invalid Token";
        http_response_code(401);
        exit;
    }
?>
