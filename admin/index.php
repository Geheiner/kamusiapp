<?php
/*
$sql =	"SELECT * FROM app;";
$stmt = $mysqli->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$results_array = $result->fetch_assoc();

	//These must be retrieved from the database
$app_id = $results_array["app_id"];
$app_secret = $results_array["app_secret"];
*/
echo $sess_user;
?>
<html>
    <head>
        <title>Kamusi Game Backend</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
    </head>
    <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="js/server_requests.js"></script>
    <script src="../js/login.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <h1>Admin Panel</h1>
    <ul class="menu">
        <li><a href="#" onclick="loadGameLanguages()">Manage Games & Languages</a></li>
        <li><a href="#" onclick="loadInterfaceLanguages()">Manage Interface Languages</a></li>
    </ul>
    <div id="settings">
    </div>
</table>
