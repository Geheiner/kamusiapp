<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
$userID = $_GET['userID'];


$WordTweetsSinceLastPost= 41;

$stmt = $mysqli->prepare("SELECT DoPost, WordTweetsSinceLastPost, PostTimeUnit  FROM users WHERE  UserID = ?;");
$stmt->bind_param("s", $userID);
$stmt->execute();
$stmt->bind_result($DoPost,$WordTweetsSinceLastPost, $PostTimeUnit);
$stmt->fetch();
$stmt->close(); 
$returnValue = array();
if($DoPost==1 || $PostTimeUnit=='0') {

    $stmt = $mysqli->prepare("UPDATE users SET WordTweetsSinceLastPost=0  WHERE  UserID = ?;");
    $stmt->bind_param("s", $userID);
    $stmt->execute();
    $stmt->close(); 

    $stmt = $mysqli->prepare("UPDATE users SET DoPost=0  WHERE  UserID = ?;");
    $stmt->bind_param("s", $userID);
    $stmt->execute();
    $stmt->close(); 
}
else {
    $WordTweetsSinceLastPost= 0;
}
echo json_encode($WordTweetsSinceLastPost);

?>
