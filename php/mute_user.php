<?php

$userID = $_GET['userID'];

//When a user reports a spam, the website admninistrator gets an e-mail.
//When the administrator clicks on the link to mute the user, this script is called.

$sql = "UPDATE users SET Mute=1 WHERE UserID=?;";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $userID);
$stmt->execute();

$stmt->close();
echo "Mute successful";

?>
