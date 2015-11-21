<?php
    require_once('./request_head.php');

    $lang = $_POST['lang'];
    $game = $_POST['game'];
    $is_active = $_POST['is_active'];

    $sql = "INSERT INTO gamelanguages (LanguageID, GameID, IsActive)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE IsActive = ?;";

    $stmt = $mysqli->prepare($sql);
    if(!$stmt) {
        echo "Prepare failed: $stmt->error";
        http_response_code(500);
        exit;
    }
    $rc = $stmt->bind_param("siii", $lang, $game, $is_active, $is_active);
    if(!$rc) {
        echo "Bind failed: $stmt->error";
        http_response_code(500);
        exit;
    }
    $rc = $stmt->execute();
    if(!$rc) {
        echo "Execute failed: $stmt->error";
        http_response_code(500);
        exit;
    }

    $stmt->close();
?>
