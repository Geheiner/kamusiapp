<?php
//global variables used throughout the application
$acceptedModes = array("1","2","3","4");
$allUsers = "allusers";
$mysqli= null;

$partOfSpeechArray= array();

<<<<<<< HEAD
$languageMap = array ("1" => "en_US", "2" => "fr_FR", "3" => "it_IT" );
=======
$languageMap = array ("1" => "en_US", "2" => "fr_FR", "3" => "it_IT");
>>>>>>> 619d74c5ece28c1bd213e783a95405fef5aed83a

$config = parse_ini_file('/var/www/passwords/config.ini');
$helsinkiUserName = $config['helusername'];
$helsinkiPassWord= $config['helpassword'];
//Login information for connecting to services

$sess_user=$config['sess_user'];
$sess_pass = $config['sess_pass'];
$base_url = "http://dev.kamusi.org:8282";


//The object that allows access to the mysql database
$mysqli = new mysqli('localhost',$config['dbusername'],$config['dbpassword'],$config['dbname']);

//All userdata necesay for logging in with kamusi services
$kamusiUser= array();



//Functions used throughout the app in order to keep track of the scores
function addXToValueInGame($userID, $language, $mode, $value, $x){
	global $mysqli;
	$stmt = $mysqli->prepare("UPDATE games SET ". $value . " = " . $value . " + ? WHERE userid=? and language = ? AND game=?;");
	$stmt->bind_param("isii", $x, $userID, $language, $mode);
	$stmt->execute();
	$stmt->close();	
}

function setXToValueInGame($userID, $language, $mode, $value, $x){
	global $mysqli;
	$stmt = $mysqli->prepare("UPDATE games SET ". $value . " = ? WHERE userid=? and language = ? AND game=?;");
	$stmt->bind_param("isii", $x, $userID, $language, $mode);
	$stmt->execute();
	$stmt->close();	
}

function addXToPendingPointsInGame($userID, $language, $mode, $x){
	global $mysqli;
	addXToValueInGame($userID, $language, $mode, "pendingpoints", $x);

}

function setXToPendingPointsInGame($userID, $language, $mode, $x){
	global $mysqli;
	setXToValueInGame($userID, $language, $mode, "pendingpoints", $x);

}

function addXToPointsInGame($userID, $language, $mode, $x) {
	global $mysqli;

	addXToValueInGame($userID, $language, $mode, "points", $x);
	addXToValueInGame($userID, $language, $mode, "pointsmonth", $x);
	addXToValueInGame($userID, $language, $mode, "pointsweek", $x);
	
	$sql = "INSERT INTO pointtime (userID, language, game, amount, ts) VALUES ";
	$sql .= "(?,?,?,?, UTC_TIMESTAMP());";

	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("siii", $userID, $language, $mode, $x);
	$stmt->execute();
	$stmt->close();	
}

function addXSubmissionsInGame($userID, $language, $mode, $x){
	global $mysqli;
	addXToValueInGame($userID, $language, $mode, "submissions", $x);
	addXToValueInGame($userID, $language, $mode, "submissionsweek", $x);
	addXToValueInGame($userID, $language, $mode, "submissionsmonth", $x);
	$sql = "INSERT INTO submissiontime (userID, language, game, amount, ts) VALUES ";
	$sql .= "(?,?,?,?, UTC_TIMESTAMP());";

	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("siii", $userID, $language, $mode, $x);
	$stmt->execute();
	$stmt->close();	

}

function giveAllConcernedUsersXPoints($concernedUsers, $x){
	global $data, $mysqli;
	foreach($concernedUsers as $user) {
		addXToPointsInGame($user, $data["language"], $data["mode"], 1);

		$returnText = $user;

		$stmt = $mysqli->prepare("UPDATE users SET NewPointsSinceLastNotification = NewPointsSinceLastNotification + ? WHERE UserID=?;");
		$stmt->bind_param("is", $x, $user);
		$stmt->execute();
		$stmt->close();
	}
}

function debugVariable($var, $name){
	echo "\n\nBegin " . $name;
	 echo print_r($var, true);
	 echo "End " . $name;
}

?>
