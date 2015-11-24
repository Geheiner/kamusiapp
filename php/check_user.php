<?php
session_start();
$userID = $_GET['userID'];
$userName = $_GET['userName'];

// Get users interface language
$stmt = $mysqli->prepare("SELECT users.interfacelanguage, locale
                          FROM users
                          INNER JOIN interfacelanguages
                          ON users.interfacelanguage=interfacelanguages.LanguageID
                          WHERE UserID = ?;");
$stmt->bind_param("s", $userID );
$stmt->execute();
$stmt->bind_result($checkResult, $locale);
$stmt->fetch();
$result = $stmt->get_result(); 

$stmt->close();

$returnValue[]= $locale;


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
        $languageArray[] = $row['LanguageID'];
    }

    // Create game entry per different game
    foreach ($acceptedModes as $mode) {
        // and per language
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
        $languageArray[] = $row['LanguageID'];
    }

    // Create game entry per different game
    foreach ($acceptedModes as $mode) {
        // And per language
        foreach ($languageArray as $language) {
            $stmt = $mysqli->prepare("INSERT INTO games (userID, game, language) VALUES(?,?,?);");
            $stmt->bind_param("sii", $userID, $mode, $language );
            $stmt->execute();
            $stmt->close();
        }
    }

    // Check if user has logged in before
    $stmt = $mysqli->prepare("SELECT firsttime FROM users WHERE UserID = ? ;");
    $stmt->bind_param("s", $userID );
    $stmt->execute();
    $stmt->bind_result($firsttime);
    $stmt->fetch();
    $result = $stmt->get_result(); 
    $stmt->close();


    // Set session variable 'lang' if not already set
    if(! isset($_SESSION['lang'])){
        $stmt = $mysqli->prepare("SELECT locale FROM interfacelanguages WHERE LanguageID = ?");
        $stmt->bind_param("s", $checkResult);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $row = $result->fetch_assoc();
        $_SESSION['lang']=$row['locale'];
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
