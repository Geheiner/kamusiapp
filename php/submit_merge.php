<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$userID = $_POST['userID'];
$mode = $_POST['mode'];
$language = $_POST['language'];
$choice = $_POST['choice'];

$pendingScore = 0;
if($choice < 3) {
    addXSubmissionsInGame($userID, $language, $mode, 1);

    $stmt = $mysqli->prepare("SELECT submissions FROM games WHERE userid=? AND game=?;");
    $stmt->bind_param("ii", $userID, $mode);
    $stmt->execute();
    $stmt->bind_result($pendingScore);
    $stmt->fetch();
    $stmt->close();

    setXToPendingPointsInGame($userID, $language, $mode, $pendingScore);
}

echo json_encode($pendingScore);
?>
