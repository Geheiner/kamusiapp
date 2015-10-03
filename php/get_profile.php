<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include 'validate_token.php';

$userID = $_GET['userID'];


// TODO Sanitize this input!!
$sql =  "SELECT * FROM users WHERE UserID='" . $userID . "';";

$stmt = $mysqli->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$profileData = $result->fetch_array(MYSQLI_ASSOC);

$jsonData = json_encode($profileData);
echo $jsonData;

?>
