<?php


$wordID = $_GET['wordID'];
$groupID = $_GET['groupID'];
$definition = $_GET['definition'];
$userID = $_GET['userID'];
$mode = $_GET['mode'];
$language = $_GET['language'];


if(!in_array($mode, $acceptedModes)) {
    die("Got a strange mode as input!". $mode);
}


// increase the number of submissions for this use
// TODO: change value to 5
addXSubmissionsInGame($userID, $language, $mode, 1);
addXToPointsInGame($userID, $language, $mode, 1);

global $mysqli;
if ($groupID == 'null') {
    $sql =  "SELECT MAX(GroupID) FROM definitions;";
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $results_array = $result->fetch_assoc();
    $stmt->close();
    $groupID = $results_array['MAX(GroupID)'] + 1;

    $sql = "UPDATE words SET DefinitionID=? WHERE ID=?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ii", $groupID, $wordID);
    $stmt->execute();
    $stmt->close();
}

$sql =  "INSERT INTO definitions (Definition, GroupID, UserID) VALUES (?,?,?);";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sis", $definition, $groupID, $userID);
$stmt->execute();
$stmt->close();

// give the user 10 pending points : the one he gets if his definition reaches consensus
addXToPendingPointsInGame($userID, $language, $mode, 10);
echo 'Success';

?>
