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

    function check_statement($stmt, $error, $message) {
        if($stmt === false) {
            http_response_code($error);
            die($message);
        }
    }

    function check_prepare($stmt) {
        check_statement($stmt, 500, "prepare() failed");
    }

    function check_bind_param($stmt) {
        check_statement($stmt, 500, "bind_param() failed");
    }

    function check_execute($stmt) {
        check_statement($stmt, 500, "execute() failed");
    }

    function check_get_result($stmt) {
        check_statement($stmt, 500, "get_result() failed");
    }
?>
