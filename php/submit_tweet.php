<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');


$data = json_decode($_POST['json'], true);


$returnText = "nothing";


//increase the number of submissions for this user
addXSubmissionsInGame($data["userID"],$data["language"], $data["mode"],1 );

$totalScoreOfTweet = 0;
$pendingScore = 02;
$concernedUsers = array();

// insert the word in the TweetContext table
$stmt = $mysqli->prepare("INSERT INTO TweetContext (TweetID, Text, Author, UserID, WordID, Good) VALUES (?,?,?,?,?,? );");
$stmt->bind_param("ssssii",  $data["tweetID"],$data["tweetText"], $data["tweetAuthor"],$data["userID"], $data["wordID"], $data["good"]);

$stmt->execute();

$stmt->close();


// Sum up the good values
$stmt = $mysqli->prepare("SELECT SUM(Good) FROM TweetContext WHERE WordID= ? AND TweetID= ?;");
$stmt->bind_param("is", $data["wordID"], $data["tweetID"]);
$stmt->execute();
$stmt->bind_result($totalScoreOfTweet);
$stmt->fetch();

$stmt->close();

// get all concerned users;
$stmt = $mysqli->prepare("SELECT DISTINCT UserID FROM TweetContext WHERE WordID= ? AND TweetID= ?;");
$stmt->bind_param("is", $data["wordID"], $data["tweetID"]);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $concernedUsers[] = $row["UserID"];
}

$stmt->close();


// Check if this tweet has been voted as bad by at least 2 users
if ($totalScoreOfTweet < -1 ) {
    $stmt = $mysqli->prepare("DELETE FROM TweetContext WHERE WordID= ? AND TweetID= ?;");
    $stmt->bind_param("is", $data["wordID"], $data["tweetID"]);
    $stmt->execute();
    $stmt->close();

// User gets notified for each point : upvotes and downvotes, but no posts for downvotes
    giveAllConcernedUsersXPoints($concernedUsers,1);
}

// We count the number of new examples validated by user. That way we will be able to show the new ones we he arrives on the link.
// after 5 upvotes, this tweet is a definite example for that word. Remove it from temp db and add it to the definitive db
if ($totalScoreOfTweet > 4 ) {
    giveAllConcernedUsersXPoints($concernedUsers,1);

    $stmt = $mysqli->prepare("INSERT INTO WordTweet (WordID, TweetID, UserID, ts) VALUES (?,?,?, UTC_TIMESTAMP());");
    $stmt->bind_param("iss", $data["wordID"], $data["tweetID"], $data["userID"]);
    $stmt->execute();
    $stmt->close(); 

    $stmt = $mysqli->prepare("INSERT INTO Tweets (TweetID, Text, Author) VALUES (?,?,?);");
    $stmt->bind_param("sss", $data["tweetID"], $data["tweetText"], $data["tweetAuthor"]  );
    $stmt->execute();
    $stmt->close(); 


    #remove the tweet from the aggregation DB
    $stmt = $mysqli->prepare("DELETE FROM TweetContext WHERE WordID= ? AND TweetID= ?;");
    $stmt->bind_param("is", $data["wordID"], $data["tweetID"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();


    $numberOfRefsForThatWord = -1;
    $stmt = $mysqli->prepare("SELECT Count(WordID) FROM WordTweet WHERE WordID= ?;");
    $stmt->bind_param("i", $data["wordID"] );
    $stmt->execute();
    $stmt->bind_result($numberOfRefsForThatWord);
    $stmt->fetch();

    $stmt->close(); 

    $stmt = $mysqli->prepare("UPDATE users SET WordTweetsSinceLastPost = WordTweetsSinceLastPost +1 WHERE UserID=?;");
    $stmt->bind_param("s", $data["userID"] );
    $stmt->execute();
    $stmt->fetch();

    $stmt->close(); 

    //We got enough tweets for this word : we don t need more.
    if($numberOfRefsForThatWord > 2) {
        $stmt = $mysqli->prepare("INSERT INTO seengames (userid, game, language,wordid, rank) VALUES (?,?,?,?, 2147483647) ;");
        $stmt->bind_param("siii",$allUsers, $data["mode"], $data["language"], $data["wordID"] );
        $stmt->execute();

        $stmt->close();

    }

}

foreach($concernedUsers as $user) {
    $stmt = $mysqli->prepare("SELECT Count(UserID) FROM TweetContext WHERE  UserID = ?;");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->bind_result($pendingScore);
    $stmt->fetch();
    $stmt->close();

    setXToPendingPointsInGame($data["userID"],$data["language"], $data["mode"],$pendingScore );
}

echo json_encode($returnText);

?>
