<?php
    require_once('./request_head.php');

    $lang = $_GET['lang'];

    // Get the first 20 matches in the ISO code db
    // either by id or by name
    $sql = "
        SELECT Ref_Name, Id FROM ISO_639_3
        WHERE Ref_Name LIKE (?) OR Id LIKE (?)
        LIMIT 20;";

    $stmt = $mysqli->prepare($sql);
    check_prepare($stmt);

    // Add wildcards to find occurences of $lang
    // in the middle of language strings
    $langwild = "%".$lang."%";
    $rc = $stmt->bind_param("ss", $langwild, $lang);
    check_bind_param($rc);

    $rc = $stmt->execute();
    check_execute($rc);

    $result = $stmt->get_result();
    check_get_result($result);

    $array = array();
    while($row = $result->fetch_assoc()) {
        $array[] = $row;
    }

    $stmt->close();

    echo json_encode($array);
?>
