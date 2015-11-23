<?php
    require_once('./request_head.php');

    $lang = $_POST['lang'];
    $locale = $_POST['locale'];

    $sql = "UPDATE interfacelanguages
            SET locale=?
            WHERE LanguageID=?;";

    $stmt = $mysqli->prepare($sql);
    check_prepare($stmt);

    $rc = $stmt->bind_param("ss", $locale, $lang);
    check_bind_param($rc);

    $rc = $stmt->execute();
    check_execute($rc);

    $stmt->close();
?>
