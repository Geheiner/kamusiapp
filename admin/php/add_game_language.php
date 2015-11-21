<?php
    require_once('./request_head.php');

    $language = $_POST['language'];
    $games = json_decode($_POST['games']);

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
?>
