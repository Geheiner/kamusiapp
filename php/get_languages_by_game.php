<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$game = $_GET['gameID'];

$sql = "SELECT LanguageID, Ref_Name 
        FROM gamelanguages 
        JOIN ISO_639_3
        ON LanguageID=Id
        WHERE GameID = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $game);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$data = array();
while($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$stmt->close();

echo json_encode($data);
