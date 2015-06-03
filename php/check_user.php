<?php
session_start();
$userID = $_GET['userID'];
$userName = $_GET['userName'];


$stmt = $mysqli->prepare("SELECT Language FROM users WHERE UserID = ? ;");
$stmt->bind_param("s", $userID );
$stmt->execute();
$stmt->bind_result($checkResult);
$stmt->fetch();
$result = $stmt->get_result(); 

$stmt->close();

$returnValue[]= $checkResult;


//if we have a newUser
if( !$checkResult){
	//Add user to database
	$stmt = $mysqli->prepare("INSERT INTO users (UserID, Username) VALUES(?,?);");
	$stmt->bind_param("ss", $userID, $userName );
	$stmt->execute();
	$stmt->close();

	//Create an entry for user for each game
	$stmt = $mysqli->prepare("SELECT ID FROM languages; ");
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();
	$languageArray = array();
	while ($row = $result->fetch_assoc()) {
		$languageArray[] = $row['ID'];
	}

	foreach ($acceptedModes as $mode) {
		foreach ($languageArray as $language) {
			$stmt = $mysqli->prepare("INSERT INTO games (userID, game, language) VALUES(?,?,?);");
			$stmt->bind_param("sii", $userID, $mode, $language );
			$stmt->execute();
			$stmt->close();
		}
	}
	$returnValue[]= "unknown user";
}
else {

	$stmt = $mysqli->prepare("SELECT firsttime FROM users WHERE UserID = ? ;");
	$stmt->bind_param("s", $userID );
	$stmt->execute();
	$stmt->bind_result($firsttime);
	$stmt->fetch();
	$result = $stmt->get_result(); 
	$stmt->close();

	$stmt = $mysqli->prepare("UPDATE users SET firsttime=0 WHERE UserID= ?;");
	$stmt->bind_param("s", $userID);
	$stmt->execute();
	$stmt->close();	

	if(! isset($_SESSION['lang'])){
		$_SESSION['lang']=$languageMap[$checkResult];
		$returnValue[]= "done";

	}
	else {
		$returnValue[]= "aleadyDoneBefore";
	}

	if($firsttime == 1) {
		//First time the user logs in, show him the settings menu
		$returnValue[]= "showSettings";
	}
	else {
		$returnValue[]= "doNotShowSettings";
		
	}

	
}




$jsonData = json_encode($returnValue);
echo $jsonData;

?>