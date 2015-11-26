<?php

ini_set('display_errors', 'On');
$userID = $_GET['userID'];
$notify = $_GET['notify'];
$post = $_GET['post'];
$language = $_GET['gameLanguage'];


$stmt = $mysqli->prepare("UPDATE users SET NotificationTimeUnit=? WHERE UserID=?;");
$stmt->bind_param("si", $notify, $userID);

$stmt->execute();
$stmt->close();

$stmt = $mysqli->prepare("UPDATE users SET PostTimeUnit=? WHERE UserID=?;");
$stmt->bind_param("si", $post, $userID);

$stmt->execute();
$stmt->close();

$stmt = $mysqli->prepare("UPDATE users SET gamelanguage=? WHERE UserID=?;");
$stmt->bind_param("si", $language, $userID);
$stmt->execute();
$stmt->close();

$stringPost = "";
$stringNotify = "";

switch ($post) {
    case '1':
    //every day at midnight
    $stringPost = "00 00 * * *"; 
    break;
    case '2':
    //every sunday
    $stringPost = "00 00 * * 0";
    break;
    case '3':
     //first of every month
    $stringPost = "00 00 1 * *";
    break;
    default:
        $stringPost = "#";
    break;
}

$output1 = shell_exec("echo \"". $stringPost . "       /usr/bin/php -f /var/www/html/php/post_timeline_local.php " . $_GET['userID'] . "\" > /var/www/tempText/posts.txt; cat /var/www/tempText/posts.txt > /var/www/tempText/both.txt 2>&1");
$stringNotify = "";
switch ($notify) {
    case '1':
    //every day at midnight
    $stringNotify = "00 00 * * *"; 
    break;
    case '2':
    //every sunday
    $stringNotify = "00 00 * * 0";  
    break;
    default:
        $stringNotify = "#";
    break;
}


shell_exec("echo \"". $stringNotify . "       /usr/bin/php -f /var/www/html/php/notification_tweet_local.php " . $_GET['userID'] . "\" > /var/www/tempText/notifications.txt; 2>&1");

$output2 = shell_exec("cat /var/www/tempText/notifications.txt >> /var/www/tempText/both.txt ; crontab /var/www/tempText/both.txt 2>&1");

$bla = shell_exec("crontab -l 2>&1");

// TODO: Remove this?
var_dump($bla);
?>
