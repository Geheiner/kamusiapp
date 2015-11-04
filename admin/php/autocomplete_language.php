<?php
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');

    $lang = $_GET['lang'];
    $token = $_GET['token'];

    // Check if request has valid token
    if(!($token == $_SESSION['api_token'])) {
        echo "Invalid Token";
        http_response_code(401);
        exit;
    }

    // Get the first 20 matches in the ISO code db
    // either by id or by name
    $sql = "
        SELECT Ref_Name, Id FROM ISO_639_3
        WHERE Ref_Name LIKE (?) OR Id LIKE (?)
        LIMIT 20;";

    $stmt = $mysqli->prepare($sql);
    $langwild = "%".$lang."%";
    $stmt->bind_param("ss", $langwild, $lang);
    $stmt->execute();
    $result = $stmt->get_result();
    $array = array();
    while($row = $result->fetch_assoc()) {
        $array[] = $row;
    }

    $stmt->close();

    echo json_encode($array);
?>
