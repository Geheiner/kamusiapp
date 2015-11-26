<?php
include 'validate_token.php';

$userID = $_GET['userID'];

// USING ROOT IS A SECURITY CONCERN

// Retrieve ID of word with first Rank greater than user_position, i.e. the first word with a sense.
$sql =  "SELECT ID As ID,
                DefinitionID As DefinitionID,
                Rank As Rank
        FROM (
            SELECT w.ID, w.DefinitionID, r.Rank
            FROM rankedwords As r
            LEFT JOIN words As w
            ON r.Word = w.Word
        ) As sq
        WHERE sq.ID IS NOT NULL
            AND sq.Rank =35
        ORDER BY(sq.Rank) LIMIT 1;";

$stmt = $mysqli->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$results_array = $result->fetch_assoc();

$word_id = $results_array['ID'];
$new_rank = $results_array['Rank'] + 1;

// increment user position
//$sql = "UPDATE users SET PositionMode1 = " . $new_rank . " WHERE UserID = " . $userID . ";";
//
// Return all definitions corersponding to this GroupID
$sql = "SELECT sq.ID As WordID,
                sq.Word,
                sq.PartOfSpeech,
                d.ID As DefinitionID,
                d.Definition,
                d.GroupID,
                d.UserID As Author
        FROM (
            SELECT *
            FROM words
            WHERE ID=?
        ) AS sq
        LEFT JOIN definitions As d
        ON sq.DefinitionID = d.GroupID
        ORDER BY Votes desc;";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $word_id);
$stmt->execute();
$result = $stmt->get_result();

$results_array = array();

while ($row = $result->fetch_assoc()) {
    $results_array[] = $row;
}

echo json_encode($results_array);
?>
