<?php

// disable the game for maintenance
$GAME_OFF = false;
if($GAME_OFF) {
    header("Location: offline.html");
}

session_start();
function generateToken() {
    // generate token from random value
    $token = md5(rand(pow(2, 32), pow(2, 33)));

    // Store token in session superglobal
    $_SESSION['token'] = $token;

    return $token;
}

$locale = isset($_SESSION['lang']) ? $_SESSION['lang'] : "en_US";


setlocale(LC_ALL, $locale .'.utf8');

/**
 * Because the .po file is named messages.po, the text domain must be named
 * that as well. The second parameter is the base directory to start
 * searching in.
 */
bindtextdomain('messages', 'locale');

/**
 * Tell the application to use this text domain, or messages.mo.
 */
textdomain('messages');

?>

<!doctype html>
<html lang="">
<head>
    <title>Kamusi GAME</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
    <link rel="stylesheet" type="text/css" media="only screen and (max-device-width: 480px)" href="css/mstyle.css"/>
</head>

<?php
$newToken = generateToken();
$gameNames = array('1' => _("Definition Game"), '2' => _("Translation Game") , '3' => _("Tweet Game"), '4'=> _("Sentence Game"));
$partOfSpeechArray= array('noun' => _("noun"), 'verb' => _('verb'), 'adjective' => _('adjective'), 'adjective_satellite' => _('adjective_satellite'), 'adverb' => _('adverb'), 'phrase' => _('phrase'));

?>
<body>
    <div id="main" >
        <div id="portal">
            <div id="welcome">
                <canvas width="930" height="550" id="animation">Your browser doesn't support HTML5.</canvas>
                <img id="logo" alt="Kamusi Logo" src="media/logo.png" onmousedown="isNewUser();">
                <img title="Choose Language" id="choose-lang" class="shaded enter" src="media/language_selector.png" onmousedown="playClick(); display_changeLanguage() ">
                <fb:login-button scope="public_profile,email" id="enterLogin" class="shaded_enterLogin" onlogin="checkLoginState();"></fb:login-button>
            </div>
            <div id="game" ng-app>
                <div id="controls">
                    <div id="controlheader">
                        <img id="controltitle" src="media/banner.png">
                    </div>
                    <div id= "ProfileHeader">
                        <div id="points-pending">In Play: 0</div>
                        <div id="points-total">Banked: 0</div>
                        <img title="Profile" id="user" class="control" src="media/user.png" onclick="playClick();display_profile();">
                    </div>
                    <img title="Invite" id="shield" class="control" src="media/invite.png" onclick="playClick();request();">
                    <img title="Share" id="gossip" class="control" src="media/balloon.png" onclick="playClick();share();">
                    <img title="About" id="information" class="control" src="media/info.png" onclick="playClick();display_about();">
                    <img title="Home" id="auction" class="control" src="media/home.png" onclick="playClick();display_welcome();">
                </div>
                <div id="gamezone1" ng-controller="InlineEditorController" ng-click="hideTooltip();">
                    <div id="gamezone-main1" >
                        <div class="entry">
                            <h1 id="title1" class="title">
                                <?php printf(_("%s"), $gameNames["1"]);; ?>
                                <img title="Info" id="information" class="controlTiny" src="media/infoSmall.png" onclick="playClick();display_info1();">
                            </h1>
                            <p id="instructions1"> </p>
                            <p id="word"></p>
                            <p id="pos"></p>
                            <p id="consensus" class="workingDefinition"></p>
                        </div>
                        <div id="definitions_wrapper">
                            <table id="definitions">
                                <tr><td>
                                    <ul>
                                    <li ng-click="toggleTooltip($event); " id="user_definition" class="inactive_definition">
                                        ✎ <?php echo gettext(" I can write the winning definition for this idea!"); ?>
                                    </li>
                                    </ul>
                                    <div class="input_tool" ng-click="$event.stopPropagation()" ng-show="showtooltip">
                                        <input id="input_tool_box" type="text" ng-model="value" ng-keypress="searchEnter($event);"/>
                                        <img title="Submit" id="SubmitDef" ng-click="submitGame1($event);" class="controlSmall" src="media/rightarrowSmall.png" onclick="">
                                    </div>
                                </td></tr>
                            </table>
                        </div>
                    <div id="hunt_wrapper">
                        <div id="hunt">
                            <a id="wiktionary" target="_blank">Wiktionary</a>
                            <a>•</a>
                            <a id="dictionary" target="_blank">Dictionary.com</a>
                            <a>•</a>
                            <a id="wordnik" target="_blank">Wordnik</a>
                        </div>
                    </div>
                    </div>
                    <div id="gamezone-footer1">
                        <div id="footer-greeting">
                            <a class="tooltip">
                                <p id="greeting"></p>
                                <span><img id="avatar" src="" width="50"></span>
                            </a>
                            <span id="login_button">
                                <fb:login-button scope="public_profile,email" onlogin="checkLoginState();"></fb:login-button>
                            </span>
                        </div>
                    </div>
                </div>
                <div id="gamezone2" ng-controller="InlineEditorController2" ng-click="hideTooltip2();">
                    <div id="gamezone-main2">
                        <div class="entry">
                        <h1 id="title2" class="title"> <?php printf(_("%s"),$gameNames["2"]); ?> <img title="Info" class="controlTiny" src="media/infoSmall.png" onclick="playClick();display_info2();"> </h1>
                            <p id="instructions2"></p>
                            <p id="translation_word"></p>
                            <p id="translation_pos"></p>
                            <p id="translation_definition" class="workingDefinition"></p>
                        </div>
                        <div id="translations_wrapper">
                            <table id="translations">
                                <tr><td>
                                <ul>
                                <li ng-click="toggleTooltip2($event)" id="user_translation" class="inactive_definition">
                                    ✎ <?php echo gettext("I can translate this word!"); ?>
                                </li>
                                </ul>
                                <div class="input_tool" ng-click="$event.stopPropagation()" ng-show="showtooltip2">
                                    <input id="translation_input_tool_box" type="text" ng-model="translation" ng-keypress="searchEnter2($event);" onFocus="this.select()"/>
                                    <img title="Submit" id="SubmitTrans" ng-click="submitGame2($event);" class="controlSmall" src="media/rightarrowSmall.png" onclick="">
                                </div>
                            </td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div id="gamezone3" ng-controller="InlineEditorController" ng-click="hideTooltip();">
                <div id="gamezone-main3" >
                    <div class="entry">
                    <h1 id="title3" class="title"> <?php printf(_("%s"),$gameNames["3"]); ?> <img title="Info" class="controlTiny" src="media/infoSmall.png" onclick="playClick();display_info3();"> </h1>
                        <p id="instructions"><?php echo _("Check ONLY the tweets that are excellent examples of THIS meaning: "); ?>   </p>
                        <p id="word3"></p>
                        <p id="pos3"></p>
                        <p id="def3" class="workingDefinition"></p>
                        <div id="definitions_wrapper">
                            <p id="twitterWords"></p>
                        </div>
                    </div>
                </div>
                <div id="gamezone-footer3">
                    <div id="footer-greeting">
                        <a class="tooltip">
                            <p id="greeting"></p>
                            <span><img id="avatar" src="" width="50"></span>
                        </a>
                    </div>
                    <div id="footer-next3">
                        <img title="Next" id="next3" ng-click="clear($event)" class="control" src="media/rightarrow.png" onclick='submitCheckBoxData("tweet"); setTimeout(get_ranked(3), 500);'>
                    </div>
                </div>
            </div>
            <div id="gamezone4" ng-controller="InlineEditorController" ng-click="hideTooltip();">
                <div id="gamezone-main4" >
                    <div class="entry">
                        <h1 id="title4" class="title"> <?php printf(_("%s"),$gameNames["4"]); ?>  </h1>
                        <p id="instructions"><?php echo _("Check the sentences that correspond well to this word: "); ?></p>
                        <p id="word4"></p>
                        <p id="pos4"></p>
                        <p id="transEnglish4"></p>
                        <p id="defSwahili4" class="workingDefinition"></p>
                        <div id="sentences_wrapper">
                            <p id="swahiliSentences"></p>
                        </div>
                    </div>
                </div>
                <div id="footer-next4">
                    <img title="Next" id="next4" ng-click="clear($event)" class="control" src="media/rightarrow.png" onclick='submitCheckBoxData("game4"); setTimeout(get_ranked(4), 500);'>
                </div>
            </div>
        </div>
        <div id="about">
            <?php include_once('./inc/about.php'); ?>
        </div>
        <div id="info1">
            <?php include_once('./inc/info1.php'); ?>
        </div>
        <div id="info2">
            <?php include_once('./inc/info2.php'); ?>
        </div>
        <div id="info3">
            <?php include_once('./inc/info3.php'); ?>
        </div>
        <div id="profile">
            <?php include_once('./inc/profile.php'); ?>
        </div>
        <div id="settings">
            <?php include_once('./inc/settings.php'); ?>
        </div>
        <div id="changeMenuLanguage">
            <?php include_once('./inc/change-menu-language.php'); ?>
        </div>
        <div id="leaderboard">
            <?php include_once('./inc/leaderboard.php'); ?>
        </div>
    </div>
</div>

<script type="text/javascript"> var token = "<?php echo $newToken; ?>"; </script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular.min.js"></script>
<script>
    //All dynamic text is references here
    ICanWrite = "<?php echo gettext(" I can write the winning definition for this idea!"); ?>"
    ICantSay = "<?php echo gettext(" I can't say - skip this one..."); ?>"
    ICanTranslate = "<?php echo gettext(" I can translate this word!"); ?>"
    keepTheGeneralSense = "<?php echo gettext(" Keep the working definition. It's spectacular as it is!"); ?>"
    generalSense = "<?php echo _("Working definition: "); ?>"
    translateTheFollowing= "<?php echo _("Translate the following word to : ")  ?>"
    yourAchievements = "<?php echo _("Your achievements for the ")  ?>"
    stringin= "<?php echo _(" in ")  ?>"
    writeOrVote= "<?php echo _("Write or vote for a definition in "); ?>"
    rankString = "<?php echo _("Rank: "); ?>"
    pointsInPlay = "<?php echo _("In Play: "); ?>"
    pointsBanked = "<?php echo _("Banked: "); ?>"

    <?php
    $js_array = json_encode($gameNames);
    echo "var gameNames = ". $js_array . ";\n";
    $js_array = json_encode($partOfSpeechArray);
    echo "var partOfSpeechArray = ". $js_array . ";\n";
    ?>
</script>
<script src="js/server_requests.js"></script>
<script src="js/login.js"></script>
<script src="js/sound.js"></script>
<script src="js/menu.js"></script>
<script src="js/animation.js"></script>
</body>
</html>
