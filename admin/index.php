<h1>Admin Panel</h1>
<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');

    // fetch list of games that are available in certain languages
    $sql = "
        SELECT ISO_639_3.Ref_Name as langName, gamelist.Name as gameName, gamelanguages.IsActive, ISO_639_3.Id as lang, gamelist.ID as game
        FROM gamelanguages
        INNER JOIN ISO_639_3
        ON lang=gamelanguages.LanguageID
        INNER JOIN gamelist
        ON game=gamelanguages.GameID
        ORDER BY lang;"

    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        $lang = $row["lang"];
        $game = $row["game"];
        $languageIdMap[$lang] = $row["langName"];
        $gameIdMap[$game] = $row["gameName"];
        $gamelanguageactive[$lang][$game] = $row["IsActive"];
    }

    // remove duplicates from languages and games
    array_unique($lang);
    array_unique($game);



    $stmt->close();


?>
<table>
<?php
    foreach($lang as $l) {
        echo "<tr>";

        foreach($game as $g) {
            echo "<td>";
            if(isset($gameLanguageActive[$l][$g])) {
                echo "Yep";
            }
            echo "</td>";
        }
        echo "</tr>";
    }

?>
</table>
