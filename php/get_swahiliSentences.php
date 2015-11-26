<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
$keyword = $_GET['keyword'];
$amount = $_GET['amount'];
$pointer = "";

set_include_path('/usr/share/pear/phpseclib/phpseclib');
include('Net/SSH2.php');


$sql = "SELECT pointer FROM game4pointer WHERE lemma = ?;";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $keyword);
$stmt->execute();
$result = $stmt->get_result();


$results_array = $result->fetch_assoc();

$pointer = $results_array['pointer'];
$stmt->close();

if($pointer != "DONE"){
    //we have to fetch something, but in a background process heyho!!


    if(empty($pointer)) {
        $pointer = "";
        $sql = "INSERT INTO game4pointer (lemma, pointer ) VALUES (?,?);";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $keyword, $pointer);
        $stmt->execute();
        $stmt->close();
    }

    $ssh = new Net_SSH2('taito.csc.fi');
    if (!$ssh->login($helsinkiUserName, $helsinkiPassWord)) {
        exit('Login Failed');
    }
    //This command executes a shell file LOCATED ON THE HCS Server.
    $command = './getDataForWord.sh ' . $keyword . " " . $amount . " " . $pointer . "  2>&1";
    $result = $ssh->exec($command);
    $nextPointeDelimiter = "NEXTPOINTER:";

    //Get the new pointer and store it in the DB
    $pointer = substr($result, strpos($result, $nextPointeDelimiter) + strlen($nextPointeDelimiter));

    $sql = "UPDATE game4pointer SET pointer= ? WHERE lemma = ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ss", $pointer, $keyword);
    $stmt->execute();
    $stmt->close();

    $results_array = array();

    $sentences = extractArray("SENTENCES");
    $sourceFiles = extractArray("SOURCEFILE");
    $sourceText = extractArray("SOURCESTEXT");

    foreach ($sentences as $index => $sentence) {
        $sql = "INSERT INTO game4sentences(keyword, sentence, author, fileinfo) VALUES (?,?,?,?);";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssss", $keyword, $sentence, $sourceText[$index], $sourceFiles[$index]);
        $stmt->execute();
        $stmt->close();
    }
}

echo json_encode($sentences);

function extractArray($inputDelimiter){
    global $result;
    $resulting_array = array();
    $beginArrayDelimiter = "<".$inputDelimiter.">";
    $endArrayDelimiter = "</".$inputDelimiter.">";
    $positionOfBeginArrayDelimiter = strpos($result, $beginArrayDelimiter) + strlen($beginArrayDelimiter);

    $sentences = substr($result, $positionOfBeginArrayDelimiter, strpos($result, $endArrayDelimiter) - $positionOfBeginArrayDelimiter);
    $array = explode("\n", $sentences);

    foreach ($array as $element ) {
        if(!ctype_space($element) && $element != ''){
            $resulting_array[] = $element;
        }
    }
    return $resulting_array;
}

?>
