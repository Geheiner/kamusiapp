<?php

ini_set('max_execution_time', 300); //300 seconds = 5 minutes

function import_file() {
	//File to import
	$filename = 'English Freq Words rank pos.csv';
	$handle = fopen($filename, "r") or die("Couldn't get handle");

	//Connect to database
	$user = 'root';
	$pass = 'WuW7wtad';
	$db = 'kamusi';

	$con = mysqli_connect('localhost', $user, $pass, $db);

	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}

	$row = 1;
    while (($data = fgetcsv($handle, 1000, ",", "\n")) !== FALSE) {
    	$word = $data[0]; $rank = $data[1]; $part = $data[2];
        $sql = "INSERT INTO rankedwords (ID, Word, PartOfSpeech, Rank) VALUES (" . $row . ",'" . $word . "','" . $part . "','" . $rank . "')";
        $retval = mysqli_query($con, $sql);
    	$row++;
    }

    fclose($handle);

	mysqli_close($con);
}

echo 'Running.../n' . '</br>';

import_file();

echo 'Finished/n';

?>