<?php
    require_once('./request_head.php');

    $sql = "SELECT LanguageID, Ref_Name, Part1, locale FROM interfacelanguages
            JOIN ISO_639_3 ON LanguageID=Id;";

    $stmt = $mysqli->prepare($sql);
    check_prepare($stmt);

    $rc = $stmt->execute();
    check_execute($rc);

    $result = $stmt->get_result();
    check_get_result($result);

    while($row = $result->fetch_assoc()) {
        $languages[$row["LanguageID"]] = [
            "name" => $row["Ref_Name"],
            "part1" => $row["Part1"],
            "locale" => $row["locale"],
        ];
    }

    $stmt->close();

    echo json_encode($languages);
?>
