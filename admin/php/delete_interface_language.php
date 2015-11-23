
<?php
    require_once('./request_head.php');

    $lang = $_POST['lang'];

    $sql = "DELETE FROM interfacelanguages
            WHERE LanguageID=?;";

    $stmt = $mysqli->prepare($sql);
    check_prepare($stmt);

    $rc = $stmt->bind_param("s", $lang);
    check_bind_param($rc);

    $rc = $stmt->execute();
    check_execute($rc);

    $stmt->close();
?>
