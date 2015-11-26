<?php

// TODO:
//
// Find out what is going on here.
// Why is this not using mysqli??

$wordID = $_GET['wordID'];
$definitionID = $_GET['definitionID'];
$userID = $_GET['userID'];

$user = 'root';
$pass = '';
$db = 'kamusi';

$con = mysqli_connect('localhost', $user, $pass, $db);

if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

$sql = "DELETE FROM definitions WHERE DefinitionID='" . $definitionID . "';";
$result = mysqli_query($con, $sql);

echo "Spam successfully removed";
?>
