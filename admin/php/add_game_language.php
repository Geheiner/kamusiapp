<?php
    require_once('./request_head.php');

    $language = $_POST['language'];
    $games = json_decode($_POST['games']);

    $sql = "INSERT INTO gamelanguages (LanguageID, GameID, IsActive) VALUES (?, ?, ?);";

    $stmt = $mysqli->prepare($sql);
    check_prepare($stmt);

    foreach($games as $game) {
        $active = 1;
        $rc = $stmt->bind_param("sii", $language, $game, $active);
        check_bind_param($rc);

        $rc = $stmt->execute();
        check_execute($rc);
    }

    $stmt->close();

    $result = array('language' => "$language", 'games' => $games);
    echo json_encode($result);
?>
