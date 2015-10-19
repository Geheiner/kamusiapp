<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include 'validate_token.php';

$sql = "SELECT DISTINCT(LanguageID), Ref_Name FROM gamelanguages
        JOIN ISO_639_3
        ON LanguageID=Id
        WHERE IsActive = 1;";

$stmt = $mysqli->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$languages = $result->fetch_assoc();

echo json_encode($languages);
?>
