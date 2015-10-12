<?php
session_start();
$_SESSION = array();


if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}


session_destroy();

error_reporting(E_ALL);

ini_set('display_errors', 'On');
$userID = $_GET['userID'];
$language = $_GET['menuLanguage'];

$stmt = $mysqli->prepare("UPDATE users SET interfacelanguage=? WHERE UserID= ?;");
$stmt->bind_param("is",  $language, $userID);
$stmt->execute();
$stmt->close();

$return = "Menu Language Changed to " . $language;
json_encode($return);

?>
