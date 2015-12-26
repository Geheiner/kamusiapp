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
$gameLanguages= array('0' => _("Undefined Language"), 'eng' => _("English"), '2' => _("French") , '3' => _("Vietnamese"), '4' => _("Swahili"), '5' => _("German"), '6' => _("Italian"), '7' => _("Esperanto"), '8' => _("Russian"), '9' => _("Kirundi"), '10' => _("Spanish"), '11' => _("Dutch"), '12' => _("Irish"));
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
                        <img title="Next" id="next1" ng-click="clear($event)" class="control" src="media/rightarrow.png" onclick='submitCheckBoxData("tweet"); setTimeout(get_ranked(3), 500);'>
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

                <div id="footer-next3">
                    <img title="Next" id="next1" ng-click="clear($event)" class="control" src="media/rightarrow.png" onclick='submitCheckBoxData("game4"); setTimeout(get_ranked(4), 500);'>
                </div>
            </div>

        </div>
        <div id="about">
            <div id="about-main">
                <div id="about1">
<?php echo _('<p><strong>About</strong><br>'); ?>
  <?php echo _('The Kamusi Project (<a href="http://kamusi.org/">http://kamusi.org/</a>) is a participatory international effort dedicated to improving knowledge of the world\'s languages. Our long term mission is to produce dictionaries and other language resources for every language, and to make those resources available for free to everyone.</p>'); ?>
<?php echo _('<p><strong>Purpose</strong><br>'); ?>
  <?php echo _('Our games are designed to learn all about your language, from you, so that we can share that knowledge with the rest of the world. Different games seek different pieces of information. What we learn in one game builds information we can use in the next. Our goal is a detailed dictionary for your language, linked to translations for all other languages, available to you for free.</p>'); ?>
<?php echo _('<p><strong>Play</strong><br>'); ?>
  <?php echo _('Our games ask you a variety of types of questions. You earn points when your answer agrees with the consensus. Your points will remain <strong><em>in play</em></strong> until consensus, when they are <strong><em>banked</em></strong> if you are on the winning team. If you do not have an answer, just skip to the next question! When enough people agree to the same answer, it is published to the live Kamusi. For definitions, the winning author will receive credit on the entry page.</p>'); ?>
<?php echo _('<p><strong>Honor Code</strong><br>'); ?>
  <?php echo _('We ask each player to give their one best answer to each question. Please do not attempt to game the game, and please alert us to suspicious items so we can remove spammers and all their spam.</p>'); ?>
<?php echo _('<p><strong>Friends</strong><br>'); ?>
  <?php echo _('The more people who play for your language, the more fun you will have - you score points more quickly, and move toward more advanced questions. If you enjoy playing, please tell your friends to join us! They&rsquo;ll have fun too, and together you will contribute to a great resource for your whole community.</p>'); ?>
    </div>
    </div>
    <div id="about-footer">
        <img title="Return" class="control" src="media/leftarrow.png" onclick="playClick();return_to_game(); ">
    </div>
</div>
<div id="info1">
            <div id="info1-main">
                <div id="bla">
<?php echo _('<p><strong>Definition Game</strong><br>'); ?>
  <?php echo _('Write a definition: 1 point<br>'); ?>
 <?php echo _(' Vote for the winning definition: 1 point<br>'); ?>
 <?php echo _(' <em><strong>Write the winning definition</strong></em>: 5 points + credit on the dictionary entry page<br>'); ?>
 <?php echo _(' Submit a <em>spam</em> definition: -10 points + watchlist</p>'); ?>
<?php echo _('<p><strong>How to write a great definition</strong><br>'); ?>
 <?php echo _(' Definitions are explanations of what a word means. They are not single words (those are synonyms). You can usually use a definition instead of the actual word. </p>'); ?>
<?php echo _('<p>Definitions are NOT translations. They are written in the same language as the term you are defining. If you are defining a word in your language, the definition should be in that language, too. Definitions should define what the term means in your language - the meaning may differ somewhat from the English definition we show of a matched English concept!</p>'); ?>
<?php echo _('<p>Stick to these rules:</p>'); ?>
<ul type="disc">
<?php echo _('  <li>Short and sweet: A definition should be as brief as possible to explain the concept, but long enough to describe it fully. If the same word has <strong><em>different meanings</em></strong>, those are <strong><em>different concepts</em></strong> with different definitions. <strong><em>A definition</em></strong> in Kamusi only explains <strong><em>one concept</em></strong>.</li>'); ?>
<?php echo _('  <li>Easy does it: Use the simplest words you can. Definitions should not force readers to jump around the dictionary more, unless technical terms are absolutely necessary.</li>'); ?>
<?php echo _('  <li>No circles: Definitions should NOT contain the word that is being defined, nor its close relatives. <em>Happiness</em> is "A feeling of joy.", not "The feeling of being happy." Definitions should not be circular - we cannot now say <em>joy</em> is "A feeling of happiness."</li>'); ?>
<?php echo _('  <li>No fluff: Do not start with "A term meaning", or "This is a", or "X refers to", etc. Style: Begin with a Capital letter and end with a period.</li>'); ?>
</ul>
<p><br>
 <?php echo _(' For <strong>English</strong>, we have 200,000 entries with "working definitions" borrowed from the <a href="http://wordnet-rdf.princeton.edu" target="_blank">Princeton WordNet</a>. Some of these definitions are great, but many can be much better. When you improve the definitions in English, we can provide better guidance for translations across languages.</p>'); ?>
<p><?php echo _(' For more insight about writing definitions, you can <a href="https://www.youtube.com/watch?v=aaqOQQOYuHA" target="_blank">watch this video</a>.</p>'); ?>
    </div>
    <div class="info-footer">
        <img title="Return" class="control" src="media/leftarrow.png" onclick="playClick();return_to_game(); ">
    </div>
</div>
</div>
<div id="info2">
            <div id="info2-main">
                <div class="bla">
<?php echo _(' <p><strong>Translation Game</strong><br>'); ?>
<?php echo _('   Submit a term that becomes the consensus: 3 points<br>'); ?>
<?php echo _('   Skip a term where the consensus is "skip" for this language: 1 point</p>'); ?>
<?php echo _(' <p>When we do not already have an equivalent term in your language for an idea that we have in our English dataset, we ask you to tell us the best way to express the same meaning.</p>'); ?>
<ul>
<?php echo _('   <li>You may not be able to give any translation. For example, many languages do not have a term for <strong><em>run</em></strong>, "a point in the game of baseball". If you cannot translate a term, <strong>skip</strong> to the next.</li>'); ?>
<?php echo _('   <li>Your term may not have exactly the same meaning as the English term. For example, English has 10 <strong><em>fingers</em></strong> and 10 <strong><em>toes</em></strong>, while Spanish has 20 <strong><em>dedos</em></strong>. For the translation game, just give us the closest equivalent, and we\'ll figure out non-parallel meanings in a different game.</li>'); ?>
<?php echo _('   <li>Your language might have more than one word that means the same thing. For example, you might use the words <strong><em>sofa</em></strong> and <strong><em>couch</em></strong> interchangeably. For the translation game, just tell us your favorite. We\'ll figure out synonyms in a different game, and you\'ll earn your points if you provide an answer that eventually reaches consensus.</li>'); ?>
</ul>

</div>
    <div class="info-footer">
        <img title="Return" class="control" src="media/leftarrow.png" onclick="playClick();return_to_game(); ">
    </div>
</div>
</div>
<div id="info3">
            <div id="info3-main">
                <div class="bla">
<?php echo _(' <p><strong>Examples Games (Tweets and Sentences)</strong><br>'); ?>
<?php echo _('   Skip an example where the consensus is "skip": 1 point<br>'); ?>
<?php echo _('   Select an example where the consensus is "select": 1 point</p>'); ?>
<?php echo _(' <p>Usage examples help people understand how a term is really used. Finding a good example can be hard, however.</p>'); ?>
<ul>
<?php echo _('   <li>Bad: "Your eyes are blue."</li>'); ?>
<?php echo _('   <li>Good: "Your eyes are the same blue as the water in the bay, and sparkle even more in the sunlight."</li>'); ?>
</ul>
<?php echo _(' <p>Because words have different senses, an example might be excellent in one context but completely wrong in another.</p>'); ?>
<ul>
<?php echo _('   <li>Good example for a different meaning of <strong><em>blue</em></strong>: "My heart is aching from the pain of missing you, I\'m feeling so blue."</li>'); ?>
</ul>
<?php echo _(' <p>In the Examples games, your goal is to find sentences that are real gems for a specific meaning of a term. The Tweet game shows you a bunch of recent postings to Twitter, while the Sentences game grabs examples from other sources. You read the definition, and click on the tweets or sentences that will be a great help for dictionary users to understand the exact meaning we show.</p>'); ?>
<ul>
<?php echo _('   <li>Do NOT click on an entry if it is a good example of a <em>different</em> meaning.</li>'); ?>
<?php echo _('   <li>Do NOT click on an entry if it kind-of, sort-of works with the meaning in question.</li>'); ?>
<?php echo _('   <li>ONLY click on an entry if it <strong><em>perfectly demonstrates</em></strong> the meaning <strong><font color="#EBA634">written in orange</font></strong>.</li>'); ?>
</ul>
<?php echo _(' <p>Remember, you will score points if you and others all agree to "skip", and you will score points if you and others all agree to "select". </p>'); ?>
<?php echo _(' <p><strong>Play at your own risk </strong><br>'); ?>
<?php echo _('   We try to filter out inappropriate content in advance. Please be warned, however, that some objectionable content might slip through our automated filters. We apologize in advance for the possibility that you might see offensive Tweets. We do not have humans reviewing the content you see in the Examples games - YOU are the filter. By ignoring offensive examples, you will help make them vanish from our data collection.</p>'); ?>
    </div>
    <div class="info-footer">
        <img title="Return" class="control" src="media/leftarrow.png" onclick="playClick();return_to_game(); ">
    </div>
</div>
</div>
<div id="profile">
    <div id="profile-main">
        <h1 id= "yourachievements"></h1>
        <div id="profile-avatar-wrapper">
            <img id="profile_avatar" src="" width="200">
        </div>
        <div id="profile-info-wrapper">
            <br>
            <table id="profile_info">
                <tr>
                    <td><?php echo _("Name"); ?></td>
                    <td id="profile_name"></td>
                </tr>
                <tr>
                    <td><?php echo _("Points"); ?></td>
                    <td id="profile_points"></td>
                </tr>
                <tr>
                    <td><?php echo _("Pending points"); ?></td>
                    <td id="pending_points"></td>
                </tr>
                <tr>
                    <td><?php echo _("Success Rate"); ?></td>
                    <td id="profile_attempts"></td>
                </tr>
            </table>
        </div>
        <div id="profile_trophies_wrapper">
            <table id="profile_trophies"></table>
        </div>
    </div>
    <div id="profile-footer">
        <img title="Return" class="controlLeft" src="media/leftarrow.png" onclick="playClick();return_to_game();">
        <img id="settings_button" title="Leaders" class="controlMiddle" src="media/medal.png" onclick="playClick();display_leaderboard();updateLeaderboard(); startAutoUpdateOfLeaderboard();">
        <img title="Settings" class="controlRight" src="media/settings.png" onclick="playClick();display_settings();">

    </div>
</div>
<div id="settings">
    <div id="settings-main">
        <img src="media/language_selector.png">
        <p><?php echo _("Hints and Help Language"); ?></p>
        <br>
        <select id="menuLanguageSettings" class="interface-lang" size="1">
        </select>
        <br><br>
        <br><br>
        <p><?php echo _("Game Language"); ?></p>
        <select id="gamelanguage" size="1">
        </select>
        <br><br>
        <p><?php echo _("Notify me about new points I earn..."); ?></p>
        <select id="notifications" size="1">
            <option><?php echo _("Real time"); ?></option>
            <option><?php echo _("Once daily"); ?></option>
            <option><?php echo _("Once weekly"); ?></option>
            <option><?php echo _("When pigs fly..."); ?></option>
        </select>
        <br><br>
        <p><?php echo _("Post achievements to my timeline..."); ?></p>

        <select id= "posts" size="1">
            <option><?php echo _("Always (Every time I have a winning entry)"); ?></option>
            <option><?php echo _("Often (Max once a day)"); ?></option>
            <option><?php echo _("Sometimes (Max once a week)"); ?></option>
            <option><?php echo _("Occasionally (Max once monthly)"); ?></option>
            <option><?php echo _("When pigs fly... (Never)"); ?></option>
        </select>
    </div>
    <br><br>
    <img title="Submit" class="control" src="media/checksign.jpg" onclick="playClick(); saveSettings();">


</div>
<div id="changeMenuLanguage">
    <br><br>
    <br><br>
    <img src="media/language_selector.png">
    <br><br>
    <br><br>
    <select id="menuLanguage" class="interface-lang" onchange="" size="1">
    <!-- Preserving the list to add language translations to the database
        <option><?php echo ("English"); ?></option>
        <option><?php echo ("Deutsch"); ?></option>
        <option><?php echo ("Español"); ?></option>
        <option><?php echo ("Esperanto"); ?></option>
        <option><?php echo ("Français"); ?></option>
        <option><?php echo ("Gaeilge"); ?></option>
        <option><?php echo ("Italiano"); ?></option>
        <option><?php echo ("Kirundi"); ?></option>
        <option><?php echo ("Kiswahili"); ?></option>
        <option><?php echo ("Nederlands"); ?></option>
        <option><?php echo ("Русский язык"); ?></option>
        <option><?php echo ("Việt"); ?></option>

        The languages are not translated yet
            <option><?php echo ("Swahili"); ?></option> -->
    </select>
    <br><br>
    <br><br>
    <div id="menuLanguage-footer">
        <img title="Submit" class="control" src="media/checksign.jpg" onclick='playClick(); saveMenuLanguage("menuLanguage");'>
    </div>
</div>
<div id="leaderboard">
    <div id="settings-outer">
        <div class="settings-inner">
            <?php echo _("Language"); ?>
            <select class="scoreSelectors" id= "scoreLanguage" onchange="updateLeaderboard(); playClick();" size="1">
                <option><?php echo _("All languages"); ?></option>
                <option><?php echo _("English"); ?></option>
                <option><?php echo _("French"); ?></option>
                <option><?php echo _("Vietnamese"); ?></option>
                <option><?php echo _("Swahili"); ?></option>
                <option><?php echo _("German"); ?></option>
                <option><?php echo _("Italiano"); ?></option>
                <option><?php echo _("Esperanto"); ?></option>
                <option><?php echo _("Russian"); ?></option>
                <option><?php echo _("Kirundi"); ?></option>
                <option><?php echo _("Spanish"); ?></option>
                <option><?php echo _("Dutch"); ?></option>
                <option><?php echo _("Irish"); ?></option>

            </select>
        </div>
        <div class="settings-inner">
            <?php echo _("Game"); ?>
            <select class="scoreSelectors" id= "scoreGame" onchange="updateLeaderboard(); playClick();" size="1">
                <option><?php echo _("All games"); ?></option>
                <option><?php printf(_("%s"),$gameNames["1"]); ?></option>
                <option><?php printf(_("%s"),$gameNames["2"]); ?></option>
                <option><?php printf(_("%s"),$gameNames["3"]); ?></option>
                <option><?php printf(_("%s"),$gameNames["4"]); ?></option>

            </select>
        </div>
        <div class="settings-inner">
            <?php echo _("TimePeriod"); ?>
            <select class="scoreSelectors" id= "scoretimePeriod" onchange="updateLeaderboard(); playClick();" size="1">
                <option><?php echo _("All time"); ?></option>
                <option><?php echo _("Last Month"); ?></option>
                <option><?php echo _("Last Week"); ?></option>
                <option><?php echo _("Last 24 Hours"); ?></option>
            </select>
        </div>
        <div class="settings-inner">
            <?php echo _("What to compare"); ?>
            <select class="scoreSelectors" id= "scoreMetric" onchange="updateLeaderboard(); playClick();" size="1">
                <option><?php echo _("Points Earned"); ?></option>
                <option><?php echo _("# of Submissions"); ?></option>
                <option><?php echo _("Success Rate"); ?></option>

            </select>
        </div>
        <div class="settings-inner">
        <input id="autoloop" class="scoreSelectors" type="checkbox" name="autoloop" value="autoloop" checked="true" ><?php echo _("Auto-Loop"); ?><br>
        </div>
    </div>
    <h1><?php echo _("Leaderboard"); ?></h1>

    <div id="leader">
        <table id="score_table">
        </table>
    </div>
    <div id="leaderboard-footer">
        <img title="Return" class="control" src="media/leftarrow.png" onclick="playClick(); display_profile();">
    </div>
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
$js_array = json_encode($gameLanguages);
echo "var gameLanguages = ". $js_array . ";\n";
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
