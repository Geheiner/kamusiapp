<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include 'validate_token.php';

$userID = $_GET['userID'];

// Query to fetch a list of interfacelanguages, as well as
// a column that contains an entry for the language that is
// currently chosen by the user
$sql = "SELECT LanguageID, Ref_Name, interfacelanguage
        FROM interfacelanguages
        JOIN ISO_639_3
        ON LanguageID = Id
        LEFT JOIN (
            SELECT interfacelanguage, UserID 
            FROM users 
            WHERE UserID=?
        )
        AS active_interface
        ON LanguageID=active_interface.interfacelanguage;";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$languages = array();
while($row = $result->fetch_assoc()) {
    $languages[] = $row;
}

$stmt->close();

echo json_encode($languages);
