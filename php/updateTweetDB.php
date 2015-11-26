<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');


$data = json_decode($_POST['json'], true);


$returnText = "nothing";


if($data["status"] == "allTweetsWereBad") {
    // If no tweets were selected, remember that

    $stmt = $mysqli->prepare("SELECT wordid, failures FROM wordfailure WHERE wordid= ?;");
    $stmt->bind_param("i", $data["wordID"]);
    $stmt->execute();
    $stmt->bind_result($wordInTable, $failures);
    $stmt->fetch();
    $stmt->close();

    if(!$wordInTable){

        $stmt = $mysqli->prepare("INSERT INTO wordfailure (wordid, failures) VALUES (?,1);");
        $stmt->bind_param("i", $data["wordID"]);

        $stmt->execute();

        $stmt->close();
    }
    else {
        // If 6 players did not select anything for this word
        if($failures > 0){
            //ignore this word from now on

            $stmt = $mysqli->prepare("INSERT INTO seengames (userid , game, wordid, language, rank) VALUES (?,?,?,?,2147483647);");
            $stmt->bind_param("siii", $allUsers, $data["mode"], $data["wordID"], $data["language"]);
            $stmt->execute();
            $stmt->close();
        }

        $stmt = $mysqli->prepare("UPDATE wordfailure SET failures = failures+1 WHERE wordid= ?;");
        $stmt->bind_param("i", $data["wordID"]);
        $stmt->execute();
        $stmt->close();
    }
}

if($data["status"] == "noTweetFound") {
    $stmt = $mysqli->prepare("INSERT INTO seengames (userid , game, wordid, language, rank) VALUES (?,?,?,?,2147483647);");
    $stmt->bind_param("siii", $allUsers, $data["mode"], $data["wordID"], $data["language"]);
    $stmt->execute();
    $stmt->close();
}


echo json_encode($returnText);

?>
