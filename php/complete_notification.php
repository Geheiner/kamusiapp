<?php

$userID = $_GET['userID'];

//Signal that a notification was completed, the flag to notify is set to off

$sql = "UPDATE users SET Notify=0 WHERE UserId= ?;";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $userID);
$stmt->execute();

$stmt->close();
?>
