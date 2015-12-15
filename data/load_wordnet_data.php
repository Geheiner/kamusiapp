<?php
ini_set('max_execution_time', 600); //300 seconds = 5 minutes

//Extract the substring of #line between #head and #tail substrings
function getField($line, $head, $tail) {
	$headpos = strpos($line, $head) + strlen($head);
	$tailpos = $headpos + strpos(substr($line, $headpos), $tail);
	return substr($line, $headpos, $tailpos - $headpos);
}

function import_file() {
	//File to import
	$filename = 'wn31.nt';
	$handle = fopen($filename, "r") or die("Couldn't get handle");

	//Connect to database
	$user = 'root';
	$pass = '';
	$db = 'kamusi';

	$con = mysqli_connect('localhost', $user, $pass, $db);

	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}

    $entryID = 0;
	$currentDefinition = ''; $currentPos = '';
    $words = array();
	
    while (!feof($handle)) {
        $buffer = fgets($handle, 4096);
        $currentEntryID = getField($buffer, '/wn31/', '-');

        if($currentEntryID != $entryID && $currentEntryID != "") {

            $sql = "INSERT INTO definitions (GroupID, Definition, UserID) VALUES ('" . $entryID . "','" . $currentDefinition . "','wordnet')";
            $retval = mysqli_query($con, $sql);

            foreach ($words as $word) {
                $sql = "INSERT INTO words (Word, PartOfSpeech, DefinitionID) VALUES ('" . $word . "','" . $currentPos . "','" . $entryID . "')";
                $retval = mysqli_query($con, $sql);
            }
            $words = array();
            $entryID = $currentEntryID;
        }

        $lineType = getField($buffer, '#', '>');

        switch ($lineType) {
        	case 'label':
        		$words[] = getField($buffer, 'label> "', '"');
        		break;
        	case 'gloss':
        		$currentDefinition = getField($buffer, 'gloss> "', '"');
        		break;
            case 'part_of_speech':
                $currentPos = getField($buffer, 'part_of_speech> <http://wordnet-rdf.princeton.edu/ontology#', '>');
                break;
        }
    }
    fclose($handle);

	mysqli_close($con);
}

echo 'Running.../n';

import_file();

echo 'Finished/n';

?>
