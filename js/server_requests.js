//This file contains all code responsible for client-server communication

var userID = "???" //"???"; //so that it works offline:  10203265649994971
userName = "???"
var wordID;
var word;
var definitionID;
var groupID;
var amountOfTweets;
var amountGame4;

//settings saved
var whenToPost;
var whenToNotify;
var gameLanguageSliderValue;

//var to remember the current language
var gameLanguage;
//remember which game is currently played
var game = 0;

//This is the overallLanguage
var siteLanguage="-1"

var translationID;

var last20Tweets = {}
var lastSwahiliSentences = {}

//Current points for the current game

function getRankedForTweets() {
    //remove previous tweet entries
    $("#twitterWords").html('');

    $(".entry").addClass("fade");
    // TODO: check why we submit hardcoded strings here
    $.getJSON("php/get_ranked.php", {userID: userID, language: gameLanguage, mode: "3"})
        .done(function(obj, status) {
            console.log("TweetResponse was : " + obj);

            groupID = obj[0].GroupID;
            wordID = obj[0].WordID;
            word = obj[0].Word;

            if(groupID == '' || wordID == '' || word == '' || obj[0].Definition == '' || obj[0].PartOfSpeech == '') {
                updateTweetDB("noTweetFound")
                    getRankedForTweets();
            } else  {
                $("#word3").html(obj[0].Word);
                $("#def3").html(generalSense + "<strong>" + obj[0].Definition + "</strong>");
                $("#pos3").html(obj[0].PartOfSpeech);

                fetchTweetsFromDB(8);
            }
            $(".entry").removeClass("fade");
        })
        .fail(function() {
            console.log("Getting ranked for Tweets failed");
        });
}

function getRankedForSwahili() {

    $("#swahiliSentences").html('');
    wordID= 12345;
    $(".entry").addClass("fade");

    $.getJSON("php/get_swahili_word.php", {userID: userID})
        .done(function(obj, status) {
            console.log("JSON DATA SWAHILI LOOKS LIKE : " + obj);

            word = obj.title;
            wordID = obj.nid;

            $("#word4").html(word);
            $("#pos4").html("Not set Yet");
            $("#transEnglish4").html("English def Not accessible yet");
            $("#defSwahili4").html("Swahili def Not accessible yet");

            //getGame4Sentences(word, 3);
            $(".entry").removeClass("fade");
        })
        .fail(function() {
            console.log("getRankedForSwahili failed");
        });
}

//One JS call to verify number of sentences available.
//Then If enough, 2 parallel calls: one to get sentences from the DB, one other to fetch new ones.
function getGame4Sentences(keyword, amount) {
    console.log("Checking if the DB contains enough sentences for this keyword");
    var xmlhttp;
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else  {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            console.log(xmlhttp.responseText);
            var numberOfSentences = JSON.parse(xmlhttp.responseText);
            if(numberOfSentences < amount ){
                //We need to fetch sentences right away in order to get to the desired numner
                if ($("#swahiliSentences").html() == ""){
                    updateBufferForDatabase(keyword, amount);
                }
                $("#swahiliSentences").html("Searching the web for new sentences, this may take some time...");
                setTimeout( function(){getGame4Sentences(keyword, amount)}, 5000)
            } else  {
                $("#swahiliSentences").html("");
                queryForSentences(keyword, amount, "local");
                updateBufferForDatabase(keyword, amount);
            }
        }
    }
    xmlhttp.open("GET","php/check_buffered_sentences.php?keyword=" + keyword , true);
    xmlhttp.send();
}

function updateBufferForDatabase(keyword, amount){
    console.log("Updating buffer...");
    var xmlhttp;
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else  {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            //console.log(xmlhttp.responseText);
            console.log("BUFFER UPDATED!!!");
        }
    }
    xmlhttp.open("GET","php/get_swahiliSentences.php?keyword=" + keyword + "&amount=" + amount , true);

    xmlhttp.send();
}

function queryForSentences(keyword, amount, source){
    console.log("Querying the helsinki DB...");
    amountGame4=amount
        var xmlhttp;
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else  {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            console.log("Returned from helsinki query: ");
            console.log(xmlhttp.responseText);
            var results_array = JSON.parse(xmlhttp.responseText);
            for( i = 0; i<amount ;i++) {
                //last20Tweets[i] = results_array[i];
                lastSwahiliSentences[i] = results_array[i];
                displayTextWithCheckboxes(lastSwahiliSentences[i].sentence,i,"swahiliSentences")
            }
        }
    }
    prefix = "php/get_swahiliSentences.php"
        if(source == "local"){
            prefix = "php/get_swahiliSentences_DB.php"
        } else  {
            prefix = "php/get_swahiliSentences.php"
        }
    xmlhttp.open("GET",prefix + "?keyword=" + keyword + "&amount=" + amount , true);
    xmlhttp.send();
}

function get_tweets(alreadyDisplayed) {
    console.log("inside the tweets function: ");
    var xmlhttp;
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else  {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            console.log("RESPONSE TEXT : " + xmlhttp.responseText + " END RESPONSE TEXT");
            var results_array = JSON.parse(xmlhttp.responseText);
            var listOfAll = results_array.filter(function(elem, pos) {
                return results_array.indexOf(elem) == pos;
            });
            realIndex = 0
                listOfAll.forEach( function(elem,pos) {
                    realIndex = alreadyDisplayed + pos;
                    last20Tweets[realIndex] = elem;
                    displayTextWithCheckboxes(elem.Text,realIndex,"twitterWords")
                }
                );

            if(realIndex == 0){
                console.log("Nothing found for this keyword");

                updateTweetDB("noTweetFound")
                    getRankedForTweets();
            }

        }
    }
    xmlhttp.open("GET","php/get_tweets.php?keyword=" + encodeURIComponent(word) + "&amount=" + (amountOfTweets - alreadyDisplayed));

    xmlhttp.send();
}

function displayTextWithCheckboxes(elemText, index, whereToInsert){
    var tweetDisplay = document.createElement("P");
    tweetDisplay.id = "tweetDisplay" + index;
    tweetDisplay.name = "elem" ;
    tweetDisplay.type = "text";

    var newInput = document.createElement("INPUT");
    newInput.id = "checkbox" + index;
    newInput.name = "checkbox" ;
    newInput.type = "checkbox";

    tweetDisplay.onclick = function(){
        newInput.checked = !newInput.checked;
        changeColorOnClick(tweetDisplay,newInput);
    };
    newInput.onchange = function(){
        changeColorOnClick(tweetDisplay,newInput);
    }

    var t = document.createTextNode(elemText);
    console.log("STYLEEEEE " + tweetDisplay.style.color);
    tweetDisplay.appendChild(newInput);
    tweetDisplay.appendChild(t);
    document.getElementById(whereToInsert).appendChild(tweetDisplay);
}

function updateTweetDB(status) {
    var xmlhttp;
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else  {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            console.log("Response from TweetDB update was: " + xmlhttp.responseText );
        }
    }
    var json_data= {"wordID":wordID, "userID":userID, "mode":game, "language":gameLanguage, "status" : status    }

    $.ajax({
        type: 'POST',
        url: 'php/updateTweetDB.php',
        data: {json: JSON.stringify(json_data)},
        dataType: 'json'
    })
    .done( function( data ) {
        console.log('done');
    })
    .fail( function( data ) {
        console.log('fail');
        console.log(data);
    });
}

function fetchTweetsFromDB(amount) {
    amountOfTweets = amount;
    var xmlhttp;
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else  {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {

            console.log("REsPONSE : " + xmlhttp.responseText + "End response");

            var results_array = JSON.parse(xmlhttp.responseText);
            var i = 0
                for( i = 0; i<amount && typeof results_array[i] !== 'undefined' && results_array[i] != null; i++) {
                    last20Tweets[i] = results_array[i];
                    displayTextWithCheckboxes(last20Tweets[i].Text,i,"twitterWords")
                }
            console.log("this was i " + i + ", this is amount : " + amount);
            if(i < amountOfTweets) {
                get_tweets( i);
            }
        }
    }
    console.log("WORD ID IS : "+ wordID);

    xmlhttp.open("GET","php/fetch_tweet_db.php?wordID=" + wordID + "&amount=" + amount);

    xmlhttp.send();
}

function submitCheckBoxData(whatToSubmit) {
    if(whatToSubmit == "tweet"){
        var allTweetsWereBad = true;
        for(var i= 0; i < amountOfTweets; i++) {
            if(document.getElementById("checkbox"+i).checked) {
                sendTweetToDB(last20Tweets[i],1)
                    allTweetsWereBad= false;
            } else  {
                sendTweetToDB(last20Tweets[i],-1);
            }
        }
        if(allTweetsWereBad){
            updateTweetDB("allTweetsWereBad")
        }
    } else  if (whatToSubmit == "game4")  {

        for(var i= 0; i < amountGame4; i++) {
            if(document.getElementById("checkbox"+i).checked) {
                sendGame4SentenceToDB(lastSwahiliSentences[i],1)
            } else  {
                sendGame4SentenceToDB(lastSwahiliSentences[i],-1);
            }
        }
    }
    if(whenToNotify == "0"){
        trigger_notification()
    }

    post_timeline();
}

function sendGame4SentenceToDB(sentence, good){
    console.log("Sending swahili results to DB:...");
    var xmlhttp;
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else  {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            console.log("DB Response t sending swahili results was:  " + xmlhttp.responseText);
            getGameScore();
        }

    }
    //wordID, sentenceID, userID, game lang
    console.log("When submitting sentence, good is : " + good);
    xmlhttp.open("GET","php/submit_sentence.php?wordID=" + wordID + "&userID=" + userID  + "&sentenceID=" + sentence.sentenceid + "&good=" + good + "&mode=" + game + "&language=" + gameLanguage, true);
    xmlhttp.send();
}

function sendTweetToDB(tweet, good){
    var xmlhttp;
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else  {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            console.log("DB esponse was:  : " + xmlhttp.responseText);
        }
    }
    var json_data= {"wordID":wordID, "tweetID":tweet.TweetID, "tweetText":tweet.Text, "userID":userID, "mode":game, "language":gameLanguage, "tweetAuthor":tweet.Author, "good" : good  }

    $.ajax({
        type: 'POST',
        url: 'php/submit_tweet.php',
        data: {json: JSON.stringify(json_data)},
        dataType: 'json'
    })
    .done( function( data ) {
        console.log('done');
        getGameScore();
    })
    .fail( function( data ) {
        console.log('fail');
        console.log(data);
    });

}

function get_ranked() {
    $(".entry").addClass("fade");

    $.getJSON("php/get_ranked.php?", {userID: userID, language: gameLanguage, mode: "1"})
        .done(function(results_array, status) {
            console.log(status);
            console.debug("GET ranked result is : " + results_array)

            clear_definitions();
            wordID = results_array[0].WordID;
            groupID = results_array[0].GroupID;

            var wordToDisplay;
            if(gameLanguage != '1' && results_array[0].trans != "Nothing Found"){
                wordToDisplay = results_array[0].trans
            } else  {
                wordToDisplay = results_array[0].Word
            }
            var underscored_word = wordToDisplay.replace(" /g", "_");

            document.getElementById("wiktionary").href = "https://en.wiktionary.org/wiki/" + underscored_word;
            document.getElementById("dictionary").href = "http://dictionary.reference.com/browse/" + underscored_word;
            document.getElementById("wordnik").href = "https://www.wordnik.com/words/" + underscored_word;
            set_word(wordToDisplay,  partOfSpeechArray[results_array[0].PartOfSpeech]);
            add_definition(-1, "? " + ICantSay, false);

            $("#consensus").html(generalSense);

            for(var i = 0; i < results_array.length ; i++) {
                if(results_array[i].Author == 'wordnet') {
                    set_consensus(results_array[i].Definition);
                    add_definition(results_array[i].DefinitionID, "▶ " + keepTheGeneralSense, false);
                }
            }
            for(var i = 0; i < results_array.length; i++) {

                if(results_array[i].Definition != undefined && results_array[i].Author != 'wordnet') {
                    add_definition(results_array[i].DefinitionID, "▶ " + results_array[i].Definition, true);
                }
            }

            definitionID = -1;
            $(".entry").removeClass("fade");
        })
        .fail(function() {
            console.log("Ranking mode 1 failed");
        });
}

function submit_definition(definition) {
    var xmlhttp;
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else  {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            console.log("submit definition returns : " + xmlhttp.responseText);
            getGameScore();
        }

    }
    console.log("When submitting definition, wordID is : " + wordID);
    xmlhttp.open("GET","php/submit_definition.php?wordID=" + wordID + "&groupID=" + groupID  + "&definition=" + definition + "&userID=" + userID + "&mode=" + game + "&language=" + gameLanguage, true);
    xmlhttp.send();
}

function isNewUser() {
    if(siteLanguage == "-1"){
        console.log("Checking if New USER");
        if(userID == "???"){
            console.log("Waiting until becoming defined!" + userID);
        } else  {
            console.log("Defined!" + userID);

            var xmlhttp;
            if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            } else {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function() {
                if (xmlhttp.readyState==4 && xmlhttp.status==200) {

                    console.log("REPONSE NEW USER : " + xmlhttp.responseText + "END");
                    obj = JSON.parse(xmlhttp.responseText);

                    if(obj[1] != "unknown user") {

                        siteLanguage=obj[0]
                            document.getElementById('menuLanguageSettings').selectedIndex= siteLanguage - 1

                            console.log("Site lanuguage is: " + siteLanguage);
                        if(obj[1] != "aleadyDoneBefore") {
                            location.reload();
                        } else {
                            if(obj[2] == "showSettings"){
                                display_settings();
                                //alert("Kamusi allows you to distinguish between the language you support when playing, called the Game Language, and the language of the Hints and the Help.\n Depending on the Game Language you have chosen, different games will be available. Try them out! ")
                            } else {
                                initialise();
                                animate_logo();
                            }
                        }
                    } else {
                        firsttime= true;
                        animate_logo_firstTime();
                    }

                }
            }
            xmlhttp.open("GET","php/check_user.php?userID=" + userID + "&userName=" + userName);
            xmlhttp.send();
        }
    }
}

function initialise() {
    set_avatar();
    add_translation_dunno('? ' + ICantSay);

    $.getJSON("php/get_profile.php", {
        userID: userID,
        token: token
    })
    .done(function(obj) {
            console.log(obj);
            whenToNotify = obj.NotificationTimeUnit;
            whenToPost = obj.PostTimeUnit;
            gameLanguageSliderValue= obj.gamelanguage;
            gameLanguage = obj.gamelanguage;
            console.log("The game language is now : " + gameLanguage);

            document.getElementById('notifications').selectedIndex = whenToNotify;
            document.getElementById('posts').selectedIndex= whenToPost;
            document.getElementById('gamelanguage').selectedIndex= gameLanguageSliderValue;

            display_welcome();
    });
}

function getGameScore(){
    $.getJSON("php/get_game_score.php", {userID: userID, mode: game, language: gameLanguage})
        .done(function(obj, status) {
            console.log(status);
            console.log(obj);

            set_profile_data(obj.points, obj.pendingpoints, (obj.points / ( parseInt(obj.submissions) + 1)).toFixed(5));
            updatePermanentMetrics(obj.points,obj.pendingpoints);
            console.log("gameLAnugage is : " + gameLanguage);
        })
        .fail(function() {
            console.log("Getting game score failed");
        });
}

function submit_vote(definition_id, vote) {
    $.getJSON("php/submit_vote.php", 
            {   wordID: wordID,
                definitionID: definition_id,
                vote: vote, 
                groupID: groupID,
                mode: game,
                language: gameLanguage})
        .done(function(result, status) {
            console.log("Submit_Vote returned : " + xmlhttp.responseText);
            getGameScore();

        })
        .fail(function() {
            console.log("Submitting vote failed");
        });
    console.log("WordID when submitting Vote : " + wordID);
}

function report_spam() {
    $.get("php/report_spam.php", {wordID: wordID, definitionID: definitionID, userID: userID})
        .done(function(result, status) {
            alert("A spam report has been sent! Thanks!" + result)
        })
        .fail(function() {
            console.log("Reporting spam failed");
        });
}

function complete_notification() {
    $.get("php/complete_notification.php", {userID: userID})
        .done(function(result, status) {
            console.log(status);
        })
        .fail(function() {
            console.log("Completing notification failed");
        });
}

function get_ranked_mode_2() {
    $(".entry").addClass("fade");

    //Lanugage is always 1 since we take english words as words to translate
    $.getJSON("php/get_ranked.php", {userID: userID, language: 1, mode: "2"})
        .done(function(obj, status) {
            console.log(status);
            console.log(obj);

            $("#translation_word").html(obj[0].Word);
            $("#translation_pos").html(partOfSpeechArray[obj[0].PartOfSpeech]);
            $("#translation_definition").html(generalSense + "<strong>" + obj[0].Definition + "</strong>");

            wordID = obj[0].WordID;
            groupID = obj[0].GroupID;
            $(".entry").removeClass("fade");
        })
        .fail(function() {
            console.log("Get ranked mode 2 failed");
        });
}

function submit_translation(translation) {
    $.get("php/submit_translation.php", {translation: translation, wordID: wordID,
        userID: userID, language: gameLanguage, mode: "2"})
        .done(function(obj, status) {
            console.log(status);
            console.log(obj);
            getGameScore();
        })
        .fail(function() {
            console.log("Submitting translation failed");
        });
}

function saveSettings() {
    menuLanguageSliderValue = $("#menuLanguageSettings option:selected").val();
    if(siteLanguage != menuLanguageSliderValue +1 ){
        console.log("Changing language...");
        saveMenuLanguage("menuLanguageSettings")
    }
    whenToNotify = $("#notifications option:selected").index();
    whenToPost = $("#posts option:selected").index();
    gameLanguageSliderValue = $("#gamelanguage option:selected").val();
    gameLanguage = $("#gamelanguage").val();
    siteLanguage = $("#menuLanguageSettings option:selected").val();

    $.get("php/save_settings.php?userID=" + userID +
            "&notify=" + whenToNotify +
            "&post=" + whenToPost +
            "&gameLanguage=" + gameLanguage).done(function(data) {
        console.log("Settings saved");
        location.reload();
    });
}

function saveMenuLanguage(whichSlider) {
    siteLanguage = $("#"+whichSlider+" option:selected").val();

    $.getJSON("php/save_menu_language.php", {userID: userID, menuLanguage: siteLanguage})
        .done(function(result, status) {
            console.log(status);
            console.log(result);
        })
        .fail(function() {
            console.log("Failed to save menuLanguage");
        });
}

function post_timeline() {
    var xmlhttp;

    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else  {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            obj = JSON.parse(xmlhttp.responseText);
            console.log("# of new definitions from user : " + obj);
            if(obj == 0){
                console.log("No activity to post");
            } else  {

                publishStory(obj)
            }
        }
    }

    xmlhttp.open("GET","php/post_timeline.php?userID=" + userID);
    xmlhttp.send();
}

function trigger_notification() {
    var xmlhttp;
    console.log("In trigger notification");

    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else  {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            console.log("NOTIFICATION RESPONSE" + xmlhttp.responseText);

        }
    }

    xmlhttp.open("GET","php/notification_tweet.php?userID=" + userID);
    xmlhttp.send();
}

function updateLeaderboard(){

    languageSelect = document.getElementById("scoreLanguage");
    scoreLanguage = languageSelect.selectedIndex;
    gameSelect = document.getElementById("scoreGame");
    scoreGame= gameSelect.selectedIndex;
    timePeriodSelect = document.getElementById("scoretimePeriod");
    scoretimePeriod = timePeriodSelect.selectedIndex;
    metricSelect = document.getElementById("scoreMetric");
    scoreMetric = metricSelect.selectedIndex;

    var xmlhttp;
    console.log("In update leaderboard");

    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else  {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            console.log("Leaderboard response : " + xmlhttp.responseText);

            table = document.getElementById("score_table");

            obj = JSON.parse(xmlhttp.responseText);
            console.log("This will be the obj");
            console.log(obj);

            max = table.rows.length;
            for(var i = 0; i < max; i++){
                console.log("DELETED : "+ i + "LENGHTH : " + table.rows.length);
                table.deleteRow(0);
            }

            for(var i = 0; i <  obj[0].length; i++) {
                var rowCount = table.rows.length;
                var row = table.insertRow(rowCount);
                rowUserID=  obj[1][i].toString();
                console.log("This is the rowCount: " + rowCount);

                if(rowUserID == userID){
                    row.className = "highlightCurrentUser";
                } else {
                    row.className = "otherUsersInTable";
                }

                row.insertCell(0).innerHTML=  '<img id="leaderPic1" src="http://graph.facebook.com/' + rowUserID + '/picture" >'        ;
                row.insertCell(1).innerHTML= obj[2][rowUserID];

                row.insertCell(2).innerHTML= obj[0][i];
                row.insertCell(3).innerHTML= rankString + (parseInt(i) + 1); //since index 0 is first rank
            }
            //add the user from before s score if use ris not in top3

            if( obj[3].rank > 4) {
                var rowCount = table.rows.length;
                var row = table.insertRow(rowCount);
                row.className = "spaceUnder";
                row.insertCell(0).innerHTML="  ";

                addScoreEntry(4,table);
            }

            //add this user s score if he is not in the top3
            if(obj[3].rank > 3) {
                addScoreEntry(3,table);
            }

            if( obj[5].id != "NOPE" ) {
                addScoreEntry(5,table);
            }
        }
    }

    xmlhttp.open("GET","php/get_user_rank.php?userID=" + userID + "&language=" + scoreLanguage + "&mode=" +scoreGame + "&metric=" + scoreMetric + "&period=" + scoretimePeriod , true);
    xmlhttp.send();

}

function addScoreEntry(indexOfArray, table){
    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);
    if(obj[indexOfArray].id == userID){
        row.className = "highlightCurrentUser";
    } else  {
        row.className = "otherUsersInTable";

    }
    row.insertCell(0).innerHTML=  '<img id="leaderPic1" src="http://graph.facebook.com/' + obj[indexOfArray].id + '/picture" >'        ;
    row.insertCell(1).innerHTML= obj[2][obj[indexOfArray].id];

    row.insertCell(2).innerHTML= obj[indexOfArray].score;
    row.insertCell(3).innerHTML= "Rank: " + obj[indexOfArray].rank;
}

function insert_game_icons(gameLanguage) {
    $.getJSON("php/get_games_by_language.php", {languageID: gameLanguage})
        .done(function(games, status) {
            console.log(status);
            console.log(games);

            $.each(games, function(index) {
                var id = games[index].GameID;
                $("#game"+id).remove();
                var name = games[index].Name;
                var html = "<img title='" + name + "' id='game" + id + "'"
                    + "class='shaded-enter' src='media/gamelogos/" + id + ".png' "
                    + "onmousedown='playClick();enter_game(" + id + ");'>";
                $(html).insertAfter("#logo");

            });
            animate_logo();
        })
        .fail(function() {
            console.log("Fetching games for language " + gameLanguage + " failed");
        });
}

function get_game_names() {
    var gamelist;
    $.getJSON("php/get_games.php")
        .done(function(games, status) {
            console.log(status);
            console.log(games);
            gamelist = games;
        })
        .fail(function() {
            console.log("Getting games failed");
        });
    // TODO: this might not work since gamelist is set in callback
    return gamelist;
}

function get_active_game_languages() {
    $.getJSON("php/get_active_languages.php")
        .done(function(languages, status) {
            console.log(status);
            console.log(languages);
            set_game_languages(languages);
        });
}

function get_interface_languages() {
    $.getJSON("php/get_interface_languages.php")
        .done(function(languages, status) {
            console.log(status);
            console.log(languages);
            set_interface_languages(languages);
        });
}
