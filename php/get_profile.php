<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include 'validate_token.php';

$userID = $_GET['userID'];

$sql = "SELECT * FROM users
        JOIN ISO_639_3
        ON Id=gamelanguage
        WHERE UserID=?;";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$profileData = $result->fetch_array(MYSQLI_ASSOC);
$stmt->close();

echo json_encode($profileData);
?>
