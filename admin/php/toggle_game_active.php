<?php
    require_once('./request_head.php');

    $lang = $_POST['lang'];
    $game = $_POST['game'];
    $is_active = $_POST['is_active'];

    $sql = "INSERT INTO gamelanguages (LanguageID, GameID, IsActive)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE IsActive = ?;";

    $stmt = $mysqli->prepare($sql);
    check_prepare($stmt);

    $rc = $stmt->bind_param("siii", $lang, $game, $is_active, $is_active);
    check_bind_param($rc);

    $rc = $stmt->execute();
    check_execute($rc);

    $stmt->close();
?>
