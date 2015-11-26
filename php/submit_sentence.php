<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');


$wordID = $_GET['wordID'];
$userID = $_GET['userID'];
$sentenceID = $_GET['sentenceID'];
$good = $_GET['good'];
$mode = $_GET['mode'];
$language = $_GET['language'];

$returnText = "nothing";


// increase the number of submissions for this user
addXSubmissionsInGame($userID,$language, $mode,1 );

$totalScoreOfSentece = 0;
$pendingScore = 02;
$concernedUsers = array();

echo "ALL INFO : ". $wordID. "sss" . $sentenceID . " sd ws " . $userID . " goood " . $good;

// insert the sentence in the sentenceContext table
$stmt = $mysqli->prepare("INSERT INTO game4context (userid, sentenceid, wordid, good) VALUES (?,?,?,?);");
$stmt->bind_param("siii",  $userID,$sentenceID, $wordID, $good);

$stmt->execute();

$stmt->close();


// Sum up the good values
$stmt = $mysqli->prepare("SELECT SUM(good) FROM game4context WHERE wordid= ? AND sentenceid= ?;");
$stmt->bind_param("ii", $wordID, $sentenceID);
$stmt->execute();
$stmt->bind_result($totalScoreOfSentece);
$stmt->fetch();

$stmt->close();

// get all concerned users;
$stmt = $mysqli->prepare("SELECT DISTINCT userid FROM game4context WHERE wordid= ? AND sentenceid= ?;");
$stmt->bind_param("ii", $wordID, $sentenceID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $concernedUsers[] = $row["userid"];
}

$stmt->close();

echo "Conerned users: ";
var_dump($concernedUsers);

//We count the number of new examples validated by user. That way we will be able to show the new ones we he arrives on the link.
// after 5 upvotes, this sentence is a definite example for that word. Remove it from temp db and add it to the definitive db
if ($totalScoreOfSentece > 4 ) {
    giveAllConcernedUsersXPoints($concernedUsers,1);

    $stmt = $mysqli->prepare("INSERT INTO wordsentence (wordid, sentenceid, userid, ts) VALUES (?,?,?, UTC_TIMESTAMP());");
    $stmt->bind_param("iis", $wordID, $sentenceID, $userID);
    $stmt->execute();
    $stmt->close(); 

    // remove the sentence from the aggregation DB
    $stmt = $mysqli->prepare("DELETE FROM game4context WHERE wordid= ? AND sentenceid= ?;");
    $stmt->bind_param("ii", $wordID, $sentenceID);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();


    $numberOfRefsForThatWord = -1;
    $stmt = $mysqli->prepare("SELECT Count(wordid) FROM wordsentence WHERE wordid= ?;");
    $stmt->bind_param("i", $wordID );
    $stmt->execute();
    $stmt->bind_result($numberOfRefsForThatWord);
    $stmt->fetch();

    $stmt->close(); 

    //increase number of submissions in order to post on facebook
    /*
    $stmt = $mysqli->prepare("UPDATE users SET WordTweetsSinceLastPost = WordTweetsSinceLastPost +1 WHERE UserID=?;");
    $stmt->bind_param("s", $data["userID"] );
    $stmt->execute();
    $stmt->fetch();

    $stmt->close(); 
     */

    //We got enough sentences for this word : we don t need more. TODO: implement
    /*
    if($numberOfRefsForThatWord > 2) {

        $stmt = $mysqli->prepare("INSERT INTO seengames (userid, game, language,wordid, rank) VALUES (?,?,?,?, 2147483647) ;");
        $stmt->bind_param("siii",$allUsers, $mode, $language, $wordID );
        $stmt->execute();

        $stmt->close(); 
    }
     */
}

foreach($concernedUsers as $user) {
    $stmt = $mysqli->prepare("SELECT Count(userid) FROM game4context WHERE  userid = ?;");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->bind_result($pendingScore);
    $stmt->fetch();
    $stmt->close();

    $stmt = $mysqli->prepare("UPDATE games SET pendingpoints = ? WHERE userid=? and language = ? AND game = ?;");
    $stmt->bind_param("isii", $pendingScore, $user, $language, $mode);
    $stmt->execute();
    $stmt->close();
}

echo json_encode($returnText);

?>
