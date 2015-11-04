<?php
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');

    $language = $_POST['language'];
    $games = json_decode($_POST['games']);
    $token = $_POST['token'];

    // Check if request has valid token
    if(!($token == $_SESSION['api_token'])) {
        echo "Invalid Token";
        http_response_code(401);
        exit;
    }

    $sql = "INSERT INTO gamelanguages (LanguageID, GameID, IsActive) VALUES (?, ?, ?)";

    $stmt = $mysqli->prepare($sql);

    foreach($games as $game) {
        $active = 1;
        $stmt->bind_param("sii", $language, $game, $active);
        $stmt->execute();
    }

    $stmt->close();

    $result = array('language' => "$language", 'games' => $games);
    echo json_encode($result);
