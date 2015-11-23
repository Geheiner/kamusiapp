<?php
    require_once('./request_head.php');

    $language = $_POST['language'];
    $locale = $_POST['locale'];

    $sql = "INSERT INTO interfacelanguages (LanguageID, locale) VALUES (?, ?);";

    $stmt = $mysqli->prepare($sql);
    check_prepare($stmt);

    $rc = $stmt->bind_param("ss", $language, $locale);
    check_bind_param($rc);

    $rc = $stmt->execute();
    check_execute($rc);

    $stmt->close();
?>
