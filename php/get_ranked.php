<?php
include 'validate_token.php';
$offsetModulo = 40;

$userID = $_GET['userID'];
$mode = $_GET['mode'];
$language = $_GET['language'];

$maximumNumberOfDefsForGame3 = 3;

$results_array = FALSE;

while($results_array === FALSE) {
    $word_id = lookForWord($userID); 
    $results_array = getDefinitions($word_id);
}

echo json_encode($results_array);

function lookForWord($userID) {
    global $offsetModulo, $mode, $language, $allUsers, $mysqli;

    //fetch the user in order to see which word is for him
    $sql = "SELECT *
            FROM games
            WHERE userid = ?
                AND language = ?
                AND game= ? ";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sii", $userID, $language, $mode );
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $user_position = $row["position"];
    $user_offset = $row["offset"];

    $stmt->close();

    echo $user_offset;
    if($user_offset > 150) {
        die("This is very likely an infinite loop in get_ranked!");
    }

    //fetch the word that has as rank user s position+offset
    $sql = "SELECT ID As ID, DefinitionID As DefinitionID, Rank As Rank
            FROM (
                SELECT w.ID, w.DefinitionID, r.Rank
                FROM rankedwords As r
                LEFT JOIN words As w
                ON r.Word = w.Word
            ) As sq
            WHERE sq.ID IS NOT NULL
                AND sq.DefinitionID IS NOT NULL
                AND sq.ID NOT IN (
                    SELECT wordid
                    FROM seengames
                    WHERE (userid=? OR userid=?)
                        AND game=?
                        AND language = ?
                )
                AND sq.Rank = ?;";


    $sum = intval($user_position) + intval($user_offset);

    $stmt = $mysqli->prepare($sql);

    if ($stmt === FALSE) {
        die ("Mysql Error: " . $mysqli->error);
    }

    $stmt->bind_param("ssiii", $userID, $allUsers, $mode, $language, $sum);

    $stmt->execute();


    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $word_id = $row["ID"];


    $stmt->close();

    $numberOfDefinitions = $result->num_rows;

    //For Game3: skip the word if there are more than 3 meanings for that word
    $conditionForGame3= ( $mode == 3 && $numberOfDefinitions > $maximumNumberOfDefsForGame3);
    if($numberOfDefinitions === 0 || $conditionForGame3 ){

        if($user_offset == 0) {
            $sql = "UPDATE games
                    SET position = position + 1
                    WHERE userid=?
                        AND language = ?
                        AND game= ?;";

            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sii", $userID, $language, $mode);
            $stmt->execute();
            $stmt->close();

            //Clean up the DB that stores the encountered words, else it become too big
            $sql = "DELETE FROM seengames
                    WHERE userid=?
                        AND language = ?
                        AND rank < ?
                        AND game= ? ;";

            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("siii", $userID, $language, $sum, $mode);
            $stmt->execute();
            $stmt->close();
        } else {
            $sql = "UPDATE games
                    SET offset = offset + 1
                    WHERE userid=?
                        AND language = ?
                        AND game = ?;";

            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sii", $userID, $language, $mode);
            $stmt->execute();
            $stmt->close(); 
        }
        return lookForWord($userID);
    } else {
        $sql = "INSERT INTO seengames (userid , game, wordid, language, rank)
                VALUES (?,?,?,?,?);";

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("siiii", $userID, $mode, $word_id, $language, $sum);
        $stmt->execute();
        $stmt->close(); 
        if($user_offset > $offsetModulo){
            $sql = "UPDATE games
                    SET offset = 0
                    WHERE userid=?
                        AND language = ?
                        AND game= ?;";

            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sii", $userID, $language, $mode);
            $stmt->execute();
            $stmt->close();
        } else {
            $sql = "UPDATE games
                    SET offset = offset + 1
                    WHERE userid=?
                        AND language = ?
                        AND game= ?;";

            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sii", $userID, $language, $mode);
            $stmt->execute();
            $stmt->close(); 
        }
        return $word_id;
    }
}

function getDefinitions($word_id){
    global $mysqli, $language;

    $sql = "SELECT sq.ID As WordID,
                   sq.ID As trans,
                   sq.Word,
                   sq.PartOfSpeech,
                   d.ID As DefinitionID,
                   d.Definition,
                   d.GroupID,
                   d.UserID As Author
            FROM (SELECT * FROM words WHERE ID=? ) AS sq
            LEFT JOIN definitions As d
            ON sq.DefinitionID = d.GroupID
            WHERE d.GroupID IS NOT NULL
            ORDER BY Votes desc;";

    $stmt = $mysqli->prepare($sql);
    if ($stmt === FALSE) {
        die ("Mysql Error: " . $mysqli->error);
    }

    $stmt->bind_param("i",  $word_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 0){
        return FALSE;
    } else {
        while ($row = $result->fetch_assoc()) {
            $results_array[] = $row;
        }

        $stmt->close();

        if($language != '1') {
            $sql = "SELECT translation
                    FROM wordtranslation
                    WHERE wordid= ?
                        AND language = ?
                    LIMIT 1;";

            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ii", $word_id, $language);
            $stmt->execute();
            $stmt->bind_result($translatedWord);
            $stmt->fetch();
            $stmt->close();

            if(!$translatedWord){
                $translatedWord = "Nothing Found";
            }
        $results_array[0]["trans"] = $translatedWord;
        }
        return $results_array;
    }
}

?>
