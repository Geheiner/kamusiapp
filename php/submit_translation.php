<?php

$wordID = $_GET['wordID'];
$userID = $_GET['userID'];
$translation = $_GET['translation'];
$mode = $_GET['mode'];
$language = $_GET['language'];

$returnValue="nothing";


//increase the number of submissions for this user
addXSubmissionsInGame($userID, $language, $mode, 1);

addXToPointsInGame($userID, $language, $mode, 1);

$sql = 	"INSERT INTO translations (LanguageID, WordID, UserID, Translation) VALUES (?,?,?,?);";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iiss", $language,$wordID, $userID ,$translation);
$stmt->execute();
$stmt->close();

$sql = 	"SELECT Count(wordid) FROM wordtranslations WHERE wordid = ? translation = ? AND language = ?;";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("isi", $wordID, $translation, $language);
$stmt->execute();
$stmt->bind_result($numberOfTranslationAlreadyInDatabase);
$stmt->fetch();
$stmt->close();

if($numberOfTranslationAlreadyInDatabase == 1){
	addXToPointsInGame($userID, $language, $mode, 10);
	$returnValue= "transAlreadyExists"
}
else {

$sql = 	"SELECT Count(Wordid) FROM translations WHERE WordID= ? AND Translation = ? AND LanguageID = ?;";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("isi", $wordID, $translation, $language);
$stmt->execute();
$stmt->bind_result($numberOfTranslationAlreadyInDatabase);
$stmt->fetch();
$stmt->close();

}

if($numberOfTranslationAlreadyInDatabase > 2 ) {

#get all concerned users;
$stmt = $mysqli->prepare("SELECT DISTINCT UserID FROM translations WHERE WordID= ? AND Translation= ? AND LanguageID = ?;");
$stmt->bind_param("isi", $wordID, $translation, $language);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
	$concernedUsers[] = $row["UserID"];
}
$stmt->close();

giveAllConcernedUsersXPoints($concernedUsers, 10);
//remove this translation from the temporal database

$sql = 	"DELETE FROM translations WHERE WordID= ? AND Translation= ? AND LanguageID = ?;";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("isi", $wordID, $translation, $language);
$stmt->execute();
$stmt->close();


}

$sql = 	"SELECT Count(WordID) FROM translations WHERE UserID = ?;";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $userID);
$stmt->execute();
$stmt->bind_result($pendingpoints);
$stmt->fetch();
$stmt->close();

setXToPendingPointsInGame($userID, $language, $mode, $pendingpoints);

echo 'We sent : ' .$language ." " . $wordID . " " . $userID . " " . $translation;;

?>