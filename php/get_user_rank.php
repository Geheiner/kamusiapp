<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

//include 'global.php';

$userID = $_GET['userID'];
$metric = $_GET['metric'];
$selectedMode = $_GET['mode'];
$language = $_GET['language'];
$timePeriod =  $_GET['period'];

$user = 'root';
$pass = '';
$db = 'kamusi';

$userAndScore= array();
$userNameByUserID = array();
$users = array();
$thisUsersScore;


//Check if the passed user is known fo security reasons

$stmt = $mysqli->prepare("SELECT * FROM users WHERE UserID = ? ");
$stmt->bind_param("s", $userID );
$stmt->execute();
$result = $stmt->get_result(); 
$stmt->close();

if($result->num_rows == 0){
    die("UserID " . $userID . " is not known!!!");
}


#get all concerned users;
$stmt = $mysqli->prepare("SELECT UserID, Username FROM users;");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $users[] = $row["UserID"];
    $userNameByUserID[$row["UserID"]] = $row["Username"];
}

$stmt->close();

//delete outdated rows from the pointtime db
$stmt = $mysqli->prepare("DELETE FROM pointtime WHERE ts < (now() - INTERVAL 1 DAY);");
$stmt->execute();
$stmt->close();

//delete outdated rows from the submissionstime db
$stmt = $mysqli->prepare("DELETE from submissiontime WHERE ts < (now() - INTERVAL 1 DAY);");
$stmt->execute();
$stmt->close();


foreach ($users as $user) {
    $value;
    switch ($metric) {
        case '0': {
            $stmt = $mysqli->prepare(getTotalXForUserStatement($user, "points", $timePeriod));
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $value = $row["total"];
            $stmt->close();
            break;
        }

        case '1': {
            $stmt = $mysqli->prepare(getTotalXForUserStatement($user, "submissions", $timePeriod));
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $value = $row["total"];
            $stmt->close();
            break;
        }

        case '2': {
            $stmt = $mysqli->prepare(getTotalXForUserStatement($user, "points", $timePeriod));
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $tempScore = $row["total"];
            $stmt->close();

            $stmt = $mysqli->prepare(getTotalXForUserStatement($user, "submissions", $timePeriod));
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            $value = $tempScore/ ($row["total"] + 1);
            break;
        }

        default: {
            die("Unexpected metric " . $metric);
            break;
        }
    }
    if($value == null) {
        $value = 0;
    }
    if($user == $userID){
        $thisUsersScore = $value;

    }

    $userAndScore[$user] = $value;
}


arsort($userAndScore);

$orderedScores = array_values($userAndScore);
$orderedUsers = array_keys($userAndScore);
$orderedUsers = array_map('strval',$orderedUsers);


$firstFiveUsers = array_slice($orderedUsers, 0, 3);
$firstFiveScores = array_slice($orderedScores, 0, 3);

$result = array();
$result[] = $firstFiveScores;
$result[] = $firstFiveUsers;
$result[] = $userNameByUserID; 
//TODO only send the entries that are needed, we are sending them all!

$userRank =  array_search($userID, $orderedUsers)+1;

$result[] = array(
    "id" => $userID,
    "score" => $thisUsersScore,
    "rank" => $userRank
);

if($userRank > 3) {

    $rankFromGuyBeforeMe= $userRank -1;
    $idOfGuyBeforeMe= $orderedUsers[$rankFromGuyBeforeMe -1];
    $scoreFromGuyBeforeMe= $orderedScores[$rankFromGuyBeforeMe -1];

    $result[] = array(
        "id" => $idOfGuyBeforeMe,
        "score" => $scoreFromGuyBeforeMe,
        "rank" => $rankFromGuyBeforeMe
    );

    $rankFromGuyAfterMe= $userRank +1;
    if($rankFromGuyAfterMe < count($orderedScores)) {
        $idOfGuyAfterMe= $orderedUsers[$rankFromGuyAfterMe -1];
        $scoreFromGuyAfterMe= $orderedScores[$rankFromGuyAfterMe -1];

        $result[] = array(
            "id" => $idOfGuyAfterMe,
            "score" => $scoreFromGuyAfterMe,
            "rank" => $rankFromGuyAfterMe
        );
    }

}

$result[] = array("id" => "NOPE");
$result[] = array("id" => "NOPE");
$result[] = array("id" => "NOPE");

//$result[] = array("myScore"=>$thisUsersScore, "myRank"=> array_search($userID, $orderedUsers)+1 );


$jsonData = json_encode($result);
echo $jsonData;

function getTotalXForUserStatement($user, $x){
    global $selectedMode, $language, $timePeriod, $acceptedModes;
    //$accetpedModes impoted from global.php

    if($timePeriod == '1') {
        $x.= "month";
    }
    if($timePeriod == '2') {
        $x.= "week";
    }
    //we use a different table for the last 24 hours so that the score is continuous
    //
    // TODO: These queries are vulnarable to attacks!!! use prepared statements!
    $sql = "SELECT SUM(t.". $x .") AS total FROM ( ";
        if($timePeriod == '3'){
            if($x == "points"){
                $x= "pointtime";
            } else {
                $x= "submissiontime";
            }
            $sql = "SELECT SUM(t.amount) AS total FROM ( ";

            if($language == '0' && $selectedMode == '0'){
                $sql .= " SELECT amount FROM ".$x
                      . " WHERE userid='".$user."' ";
            } else if( $language == '0') {
                $sql .= " SELECT amount FROM ".$x
                      . " WHERE userid='".$user
                      . "' AND game= " . $selectedMode ." ";
            } else if ($selectedMode == '0') {
                $sql .= " SELECT amount FROM ".$x
                      . " WHERE userid='".$user
                      . "' AND language= " . $language ." ";
            } else {
                $sql .= " SELECT amount FROM ".$x
                      . " WHERE userid='".$user
                      . "' AND language= ".$language
                      . " AND game= " . $selectedMode ." ";
            }
        } else {
        //rank of everything
            if($language == '0' && $selectedMode == '0'){
                $sql .= " SELECT ". $x
                      . " FROM games WHERE userid='".$user."' ";
            } else if($selectedMode == '0'){
                $sql .= " SELECT ". $x
                      . " FROM games WHERE userid='".$user
                      . "' AND language=" . $language . " ";
            } else if( $language == '0') {
                $sql .= " SELECT ".$x
                      . " FROM games WHERE userid='".$user
                      . "' AND game = ". $selectedMode. " ";
            } else {
                $sql .= " SELECT ".$x
                      . " FROM games WHERE userid='".$user
                      . "' AND language=". $language
                      . " AND game = ".$selectedMode." ";
            }
        }
        $sql .= " ) t;";
    return $sql;
}

?>
