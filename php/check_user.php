<?php
session_start();
$userID = $_GET['userID'];
$userName = $_GET['userName'];


$stmt = $mysqli->prepare("SELECT interfacelanguage FROM users WHERE UserID = ? ;");
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
    $stmt = $mysqli->prepare("SELECT LanguageID FROM gamelanguages; ");
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
} else {
    //Create an entry for user for each game
    $stmt = $mysqli->prepare("SELECT LanguageID FROM gamelanguages; ");
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
    $stmt = $mysqli->prepare("SELECT firsttime FROM users WHERE UserID = ? ;");
    $stmt->bind_param("s", $userID );
    $stmt->execute();
    $stmt->bind_result($firsttime);
    $stmt->fetch();
    $result = $stmt->get_result(); 
    $stmt->close();


    if(! isset($_SESSION['lang'])){
        $stmt = $mysqli->prepare("SELECT Part1 FROM ISO_639_3 WHERE Id = ?");
        $stmt->bind_param("s", $checkResult);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $row = $result->fetch_assoc();
        $_SESSION['lang']=$row['Part1'];
        $returnValue[]= "done";
    } else {
        $returnValue[]= "languageAlreadySet";
        $stmt = $mysqli->prepare("UPDATE users SET firsttime=0 WHERE UserID= ?;");
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        $stmt->close();
    }

    if($firsttime == 1) {
        //First time the user logs in, show him the settings menu
        $returnValue[]= "showSettings";
    } else {
        $returnValue[]= "doNotShowSettings";
    }
}

echo json_encode($returnValue);
?>
