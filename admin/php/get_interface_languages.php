<?php
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');

    $sql = "SELECT LanguageID, Ref_Name FROM interfacelanguages
            JOIN ISO_639_3 ON LanguageID=Id;";

    $token = $_GET['token'];

    // Check if request has valid token
    if(!($token == $_SESSION['api_token'])) {
        echo "Invalid Token";
        http_response_code(401);
        exit;
    }

    $stmt = $mysqli->prepare($sql);

    if($stmt) {
        $stmt->execute();
    } else {
        die("Unable to prepare: ".var_dump($mysqli));
    }
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        $languages[$row["LanguageID"]] = $row["Ref_Name"];
    }

    $stmt->close();

    echo json_encode($languages);
?>
