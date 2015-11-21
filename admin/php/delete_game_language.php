<?php
    require_once('./request_head.php');

    $lang = $_POST['lang'];

    $sql = "DELETE FROM gamelanguages
            WHERE LanguageID=?;";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $lang);
    $stmt->execute();
?>
