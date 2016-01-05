<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$userID = $_GET['userID'];
$mode = $_GET['mode'];
$language = $_GET['language'];

// Create game entry in games table to keep track of game statistics
$sql = "INSERT INTO games
        (userid, game, language)
        VALUES (?, ?, ?)";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iis", $userID, $mode, $language);
$stmt->execute();
$result = $stmt->get_result();

/*
 * This script is only returning temporary test data
 */

$wordpair1 = [
    "w1" => [
        "word" => "English 1",
        "pos" => "noun",
        "definition" => "Example definition",
        "example" => "Example example sentence"
    ],
    "w2" => [
        "word" => "Vietnamese 1",
        "pos" => "noun",
        "example" => "Example example sentence"
    ]
];

$wordpair2 = [
    "w1" => [
        "word" => "English 2",
        "pos" => "noun",
        "definition" => "Example definition",
        "example" => "Example example sentence"
    ],
    "w2" => [
        "word" => "Vietnamese 2",
        "pos" => "noun",
        "example" => "Example example sentence"
    ]
];

$wordpair3 = [
    "w1" => [
        "word" => "English 3",
        "pos" => "noun",
        "definition" => "Example definition",
        "example" => "Example example sentence"
    ],
    "w2" => [
        "word" => "Vietnamese 3",
        "pos" => "noun",
        "example" => "Example example sentence"
    ]
];

$i = rand(0, 2);

$result = array($wordpair1, $wordpair2, $wordpair3);

echo json_encode($result[$i]);
?>
