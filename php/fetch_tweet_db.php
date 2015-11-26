<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
$wordID = $_GET['wordID'];
$amount = $_GET['amount'];

$sql = "SELECT DISTINCT TweetID, Text, Author
        FROM TweetContext
        WHERE WordID= ?;";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $wordID );
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$results_array = array();

while ($row = $result->fetch_assoc()) {
    $results_array[] = $row;
}

echo json_encode($results_array);
?>
