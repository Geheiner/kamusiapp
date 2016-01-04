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

$returnValue[] = $checkResult;

//if we have a newUser
if( !$checkResult){
    //Add user to database
    $stmt = $mysqli->prepare("INSERT INTO users (UserID, Username) VALUES(?,?);");
    $stmt->bind_param("ss", $userID, $userName );
    $stmt->execute();
    $stmt->close();

    $returnValue[]= "unknown user";
} else {
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
