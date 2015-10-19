<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');

    $language = $_POST['language'];
    $games = json_decode($_POST['games']);

    $sql = "INSERT INTO gamelanguages (LanguageID, GameID, IsActive) VALUES (?, ?, ?)";

    $stmt = $mysqli->prepare($sql);

    for($i = 0; $i < count($games); $i++) {
        $active = 1;
        $stmt->bind_param("sii", $language, $games[$i], $active);
        $stmt->execute();
    }

    $stmt->close();

    $result = array('language' => "$language", 'games' => $games);
    echo json_encode($result);
