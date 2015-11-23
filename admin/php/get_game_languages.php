<?php
    require_once('./request_head.php');

    // fetch list of games that are available in certain languages
    $sql = "
        SELECT 
            ISO_639_3.Ref_Name as langName,
            gamelist.Name as gameName,
            gamelanguages.IsActive,
            ISO_639_3.Id as lang,
            gamelist.ID as game
        FROM gamelanguages
        INNER JOIN ISO_639_3
        ON ISO_639_3.Id=gamelanguages.LanguageID
        INNER JOIN gamelist
        ON gamelist.ID=gamelanguages.GameID
        ORDER BY lang;";

    $stmt = $mysqli->prepare($sql);
    check_prepare($stmt);

    $rc = $stmt->execute();
    check_execute($rc);

    $result = $stmt->get_result();
    check_get_result($result);

    while($row = $result->fetch_assoc()) {
        $lang = $row["lang"];
        $game = $row["game"];
        $languageIdMap[$lang] = $row["langName"];
        $gameIdMap[$game] = $row["gameName"];
        $gameLanguageActive[$lang][$game] = $row["IsActive"];
    }

    // remove duplicates from languages and games
    array_unique($languageIdMap);
    array_unique($gameIdMap);

    $stmt->close();

    echo json_encode(array(
        "languageIdMap"=>$languageIdMap,
        "gameIdMap"=>$gameIdMap,
        "gameLanguageActive"=>$gameLanguageActive));
?>
