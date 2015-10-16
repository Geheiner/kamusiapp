<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include 'validate_token.php';

$sql = "SELECT * FROM gamelist;";
$stmt = $mysqli->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$games = $result->fetch_assoc();

echo json_encode($games);
?>
