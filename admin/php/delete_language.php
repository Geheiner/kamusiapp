<?php
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');

    $lang = $_POST['lang'];
    $token = $_POST['token'];

    // Check if request has valid token
    if(!($token == $_SESSION['api_token'])) {
        echo "Invalid Token";
        http_response_code(401);
        exit;
    }

    $sql = "DELETE FROM gamelanguages
            WHERE LanguageID=?;";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $lang);
    $stmt->execute();
?>
