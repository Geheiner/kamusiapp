<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$language = $_GET['languageID'];

$sql = "SELECT GameID, gamelist.Name
        FROM gamelanguages
        JOIN gamelist
        ON GameID=ID
        WHERE LanguageID = ? AND IsActive=1";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $language);
$stmt->execute();
$result = $stmt->get_result();
$data = array();
while($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$stmt->close();

echo json_encode($data);
?>
