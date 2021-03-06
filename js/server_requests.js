//This file contains all code responsible for client-server communication

var userID = "???"; //"???"; //so that it works offline:  10203265649994971
userName = "???";
var wordID;
var word;
var definitionID;
var groupID;
var amountOfTweets;
var amountGame4;

//settings saved
var whenToPost;
var whenToNotify;

//var to remember the current language
var gameLanguage;
//remember which game is currently played
var game = 0;

//This is the overallLanguage
var siteLanguage = "-1";

var translationID;

var last20Tweets = {};
var lastSwahiliSentences = {};


//One JS call to verify number of sentences available.
//Then If enough, 2 parallel calls: one to get sentences from the DB, one other to fetch new ones.
function getGame4Sentences(keyword, amount) {
    console.log("Checking if the DB contains enough sentences for this keyword");
    $.getJSON("php/check_buffered_sentences.php", {keyword: keyword})
        .done(function(numberOfSentences, textStatus) {
            console.log(textStatus);
            console.log(numberOfSentences);

            if(numberOfSentences < amount ){
                //We need to fetch sentences right away in order to get to the desired numner
                if ($("#swahiliSentences").html() === ""){
                    updateBufferForDatabase(keyword, amount);
                }
                $("#swahiliSentences").html("Searching the web for new sentences, this may take some time...");
                setTimeout(function() {
                    getGame4Sentences(keyword, amount);
                }, 5000);
            } else  {
                $("#swahiliSentences").html("");
                queryForSentences(keyword, amount, "local");
                updateBufferForDatabase(keyword, amount);
            }
        })
        .fail(function() {
            console.log("Verifying number of sentences failed");
        });
}

function updateBufferForDatabase(keyword, amount){
    console.log("Updating buffer...");
    $.get("php/get_swahiliSentences.php", {keyword: keyword, amount: amount})
        .done(function(result, textStatus) {
            console.log(textStatus);
            console.log("Buffer Updated");
        })
        .fail(function() {
            console.log("Failed to update buffer");
        });
}

function queryForSentences(keyword, amount, source){
    console.log("Querying the helsinki DB...");
    amountGame4=amount;

    suffix = "";
    if(source == "local"){
        suffix = "_DB";
    }

    $.getJSON("php/get_swahiliSentences" + suffix + ".php", {keyword: keyword, amount: amount})
        .done(function(results_array, textStatus) {
            console.log(textStatus);
            console.log(results_array);
            for( i = 0; i<amount ;i++) {
                lastSwahiliSentences[i] = results_array[i];
                displayTextWithCheckboxes(lastSwahiliSentences[i].sentence,i,"swahiliSentences");
            }
        })
        .fail(function() {
            console.log("Querying for sentences failed");
        });
}

function getTweets(alreadyDisplayed) {
    var totalAmount = amountOfTweets - alreadyDisplayed;
    $.getJSON("php/get_tweets.php", {keyword: encodeURIComponent(word), amount: totalAmount})
        .done(function(results_array, textStatus) {
            console.log(textStatus);
            console.log(results_array);
            var listOfAll = results_array.filter(function(elem, pos) {
                return results_array.indexOf(elem) == pos;
            });
            realIndex = 0;
            listOfAll.forEach( function(elem,pos) {
                realIndex = alreadyDisplayed + pos;
                last20Tweets[realIndex] = elem;
                displayTextWithCheckboxes(elem.Text,realIndex,"twitterWords");
            }
            );

            if(realIndex === 0){
                console.log("Nothing found for this keyword");
                updateTweetDB("noTweetFound");
                getRanked(3);
            }
        })
        .fail(function(jqXHR, textStatus) {
            console.log("Getting tweets failed: " + textStatus);
        });
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
    };

    var t = document.createTextNode(elemText);
    tweetDisplay.appendChild(newInput);
    tweetDisplay.appendChild(t);
    document.getElementById(whereToInsert).appendChild(tweetDisplay);
}

function updateTweetDB(status) {
    var json_data= {
        wordID: wordID,
        userID: userID,
        mode: game,
        language: gameLanguage,
        "status" : status
    };

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
    $.getJSON("php/fetch_tweet_db.php", {wordID: wordID, amount: amount})
        .done(function(results_array, textStatus) {
            console.log("Response : " + results_array + "End response");
            var i = 0;
            for( i = 0; i<amount && typeof results_array[i] !== 'undefined' && results_array[i] !== null; i++) {
                last20Tweets[i] = results_array[i];
                displayTextWithCheckboxes(last20Tweets[i].Text,i,"twitterWords");
            }
            console.log("this was i " + i + ", this is amount : " + amount);
            if(i < amountOfTweets) {
                getTweets(i);
            }
        })
        .fail(function(jqXHR, textStatus) {
            console.log("Fetching tweets from DB failed: " + textStatus);
        });
}

function submitCheckBoxData(whatToSubmit) {
    var i;
    if(whatToSubmit == "tweet"){
        var allTweetsWereBad = true;
        for(i = 0; i < amountOfTweets; i++) {
            if(document.getElementById("checkbox"+i).checked) {
                sendTweetToDB(last20Tweets[i],1);
                allTweetsWereBad= false;
            } else  {
                sendTweetToDB(last20Tweets[i],-1);
            }
        }
        if(allTweetsWereBad){
            updateTweetDB("allTweetsWereBad");
        }
    } else  if (whatToSubmit == "game4")  {
        for( i = 0; i < amountGame4; i++) {
            if(document.getElementById("checkbox"+i).checked) {
                sendGame4SentenceToDB(lastSwahiliSentences[i],1);
            } else  {
                sendGame4SentenceToDB(lastSwahiliSentences[i],-1);
            }
        }
    }
    if(whenToNotify == "0"){
        triggerNotification();
    }

    postTimeline();
}

function sendGame4SentenceToDB(sentence, good){
    console.log("Sending swahili results to DB:...");
    var json_data = {
        wordID: wordID,
        userID: userID,
        sentenceID: sentence.sentenceID,
        good: good,
        mode: game,
        language: gameLanguage
    };
    $.get("php/submit_sentence.php", json_data)
        .done(function(result, textStatus) {
            console.log("DB Response t sending swahili results was:  " + result);
            getGameScore();
        })
        .fail(function(jqXHR, textStatus) {
            console.log("Sending game for sentence to DB failed: " + textStatus);
        });

    console.log("When submitting sentence, good is : " + good);
}

function sendTweetToDB(tweet, good){
    var json_data = {
        wordID: wordID,
        tweetID: tweet.TweetID,
        tweetText: tweet.Text,
        userID: userID,
        mode: game,
        language: gameLanguage,
        tweetAuthor: tweet.Author,
        good : good
    };

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

function getRanked(gameId) {
    // Hide game area
    $(".entry").addClass("fade");
    if(gameId == 1) {
        $("#instructions1").html(writeOrVote + gameLanguageName);

        $.getJSON("php/get_ranked.php?", {userID: userID, language: gameLanguage, mode: gameId})
            .done(function(results_array, textStatus) {
                console.log(textStatus);
                console.debug("GET ranked result is : ");
                console.debug(results_array);

                clear_definitions();
                wordID = results_array[0].WordID;
                groupID = results_array[0].GroupID;

                var wordToDisplay;
                if(gameLanguage != 'eng' && results_array[0].trans != "Nothing Found"){
                    wordToDisplay = results_array[0].trans;
                } else  {
                    wordToDisplay = results_array[0].Word;
                }
                var underscored_word = wordToDisplay.replace(" /g", "_");

                $("#wiktionary").attr("href", "https://en.wiktionary.org/wiki/" + underscored_word);
                $("#dictionary").attr("href", "http://dictionary.reference.com/browse/" + underscored_word);
                $("#wordnik").attr("href", "https://www.wordnik.com/words/" + underscored_word);
                set_word(wordToDisplay,  partOfSpeechArray[results_array[0].PartOfSpeech]);
                add_definition(-1, "? " + ICantSay, false);

                $("#consensus").html(generalSense);

                var i;
                for(i = 0; i < results_array.length ; i++) {
                    if(results_array[i].Author == 'wordnet') {
                        set_consensus(results_array[i].Definition);
                        add_definition(results_array[i].DefinitionID, "▶ " + keepTheGeneralSense, false);
                    }
                }

                for(i = 0; i < results_array.length; i++) {
                    if(results_array[i].Definition !== undefined && results_array[i].Author != 'wordnet') {
                        add_definition(results_array[i].DefinitionID, "▶ " + results_array[i].Definition, true);
                    }
                }

                definitionID = -1;
                $(".entry").removeClass("fade");
            })
            .fail(function(jqXHR, textStatus) {
                console.log("Ranking mode 1 failed");
                console.log(jqXHR);
                console.log(textStatus);
            });
    } else if(gameId == 2) {
        $("#instructions2").html(translateTheFollowing + gameLanguageName);

        $.getJSON("php/get_ranked.php", {userID: userID, language: gameLanguage, mode: gameId})
            .done(function(obj, textStatus) {
                console.log(textStatus);
                console.log(obj);

                $("#translation_word").html(obj[0].Word);
                $("#translation_pos").html(partOfSpeechArray[obj[0].PartOfSpeech]);
                $("#translation_definition").html(generalSense + "<strong>" + obj[0].Definition + "</strong>");

                wordID = obj[0].WordID;
                groupID = obj[0].GroupID;
                $(".entry").removeClass("fade");
            })
            .fail(function(jqXHR, textStatus) {
                console.log("Get ranked mode 2 failed");
                console.log(jqXHR);
                console.log(textStatus);
            });
    } else if(gameId == 3) {
        //remove previous tweet entries
        $("#twitterWords").html('');

        $.getJSON("php/get_ranked.php", {userID: userID, language: gameLanguage, mode: gameId})
            .done(function(obj, textStatus) {
                console.log("TweetResponse was : " + obj);

                groupID = obj[0].GroupID;
                wordID = obj[0].WordID;
                word = obj[0].Word;

                if(groupID === '' || wordID === '' || word === '' || obj[0].Definition === '' || obj[0].PartOfSpeech === '') {
                    updateTweetDB("noTweetFound");
                    getRanked(3);
                } else  {
                    $("#word3").html(obj[0].Word);
                    $("#def3").html(generalSense + "<strong>" + obj[0].Definition + "</strong>");
                    $("#pos3").html(obj[0].PartOfSpeech);

                    fetchTweetsFromDB(8);
                }
                $(".entry").removeClass("fade");
            })
            .fail(function(jqXHR, textStatus) {
                console.log("Getting ranked for Tweets failed");
                console.log(jqXHR);
                console.log(textStatus);
            });
    } else if(gameId == 4) {
        $("#swahiliSentences").html('');
        wordID= 12345;

        $.getJSON("php/get_swahili_word.php", {userID: userID})
            .done(function(obj, textStatus) {
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
            .fail(function(jqXHR, textStatus) {
                console.log("getRankedForSwahili failed");
                console.log(jqXHR);
                console.log(textStatus);
            });
    } else if(gameId == 5) {
        $.getJSON("php/get_merge_word.php", {userID: userID, language: gameLanguage, mode: gameId})
            .done(function(wordpair, textStatus) {
                console.log("Successfully returned wordpair for merging:");
                console.log(wordpair);
                $("#merge-word-left").html(wordpair.w1.word);
                $("#merge-word-right").html(wordpair.w2.word);
                $("#merge-pos-left").html(wordpair.w1.pos);
                $("#merge-pos-right").html(wordpair.w2.pos);
                $("#merge-example-left").html(wordpair.w1.example);
                $("#merge-example-right").html(wordpair.w2.example);

                setTimeout($(".entry").removeClass("fade"), 1000);
            })
            .fail(function(jqXHR, textStatus) {
                console.log("Getting ranked merge game failed");
                console.log(jqXHR);
                console.log(textStatus);
            });
    } else {
        console.log("Getting ranked failed: Unknown gameId");
    }
}

function submitMerge(choice) {
    $(".entry").addClass("fade");

    var json_data = {
        userID: userID,
        mode: game,
        language: gameLanguage,
        choice: choice
    };

    $.post("php/submit_merge.php", json_data)
        .done(function(result, textStatus) {
            console.log("Submitting merge success");
            getRanked(game);
            getGameScore();
        })
        .fail(function(jqXHR, textStatus) {
            console.log("Submitting merge failed");
        });
}

function submitDefinition(definition) {
    var json_data = {
        wordID: wordID,
        groupID: groupID,
        definition: definition,
        userID: userID,
        mode: game, 
        language: gameLanguage
    };
    $.get("php/submit_definition.php", json_data)
        .done(function(result, textStatus) {
            console.log(textStatus, result);
            getGameScore();
        })
        .fail(function(jqXHR, textStatus) {
            console.log("Submitting definition failed:" + textStatus);
        });
}

function isNewUser() {
    if(siteLanguage == "-1"){
        console.log("Checking if New USER");
        if(userID == "???"){
            console.log("Waiting until becoming defined!" + userID);
        } else {
            console.log("Defined!" + userID);
            $.getJSON("php/check_user.php", {userID: userID, userName: userName})
                .done(function(obj, textStatus) {
                    console.log(textStatus);
                    console.log(obj);

                    if(obj[1] != "unknown user") {
                        siteLanguage=obj[0];
                        $('#menuLanguageSettings').prop("selectedIndex", siteLanguage - 1);
                        console.log("Site lanuguage is: " + siteLanguage);
                        if(obj[1] != "languageAlreadySet") {
                            // TODO: fix this
                            location.reload();
                        } else {
                            if(obj[2] == "showSettings"){
                                display_settings();
                            } else {
                                initialise();
                                animate_logo();
                            }
                        }
                    } else {
                        firsttime= true;
                        animate_logo();
                    }
            })
            .fail(function(jqXHR, textStatus) {
                console.log("Checking user failed: " + textStatus);
                console.log("Checking user failed: " + jqXHR.responseText);
            });
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
    .done(function(user) {
            console.log(user);
            gameLanguage = user.gamelanguage;
            gameLanguageName = user.Ref_Name;
            console.log("The game language is : " + gameLanguage);

            console.log("set NotificationTimeUnit: " + user.NotificationTimeUnit);
            $('#notifications').prop('selectedIndex', user.NotificationTimeUnit);
            console.log("set PostTimeUnit: " + user.PostTimeUnit);
            $('#posts').prop('selectedIndex', user.PostTimeUnit);

            display_welcome();
    })
    .fail(function() {
        console.log("Initializing user failed");
    });
}

function getGameScore(){
    $.getJSON("php/get_game_score.php", {userID: userID, mode: game, language: gameLanguage})
        .done(function(obj, textStatus) {
            console.log("Getting score success");
            console.log(textStatus);
            console.log(obj);

            set_profile_data(obj.points, obj.pendingpoints, (obj.points / ( parseInt(obj.submissions) + 1)).toFixed(5));
            updatePermanentMetrics(obj.points,obj.pendingpoints);
        })
        .fail(function() {
            console.log("Getting game score failed");
        });
}

function submitVote(definition_id, vote) {
    $.getJSON("php/submit_vote.php", 
            {   wordID: wordID,
                definitionID: definition_id,
                vote: vote, 
                groupID: groupID,
                mode: game,
                language: gameLanguage})
        .done(function(result, textStatus) {
            console.log("Submit_Vote returned : " + result);
            getGameScore();

        })
        .fail(function() {
            console.log("Submitting vote failed");
        });
    console.log("WordID when submitting Vote : " + wordID);
}

function reportSpam() {
    $.get("php/report_spam.php", {wordID: wordID, definitionID: definitionID, userID: userID})
        .done(function(result, textStatus) {
            alert("A spam report has been sent! Thanks!" + result);
        })
        .fail(function() {
            console.log("Reporting spam failed");
        });
}

// TODO This function appears to be unused and should probably be removed
function completeNotification() {
    $.get("php/complete_notification.php", {userID: userID})
        .done(function(result, textStatus) {
            console.log(textStatus);
        })
        .fail(function() {
            console.log("Completing notification failed");
        });
}

function submitTranslation(translation) {
    $.get("php/submit_translation.php", {translation: translation, wordID: wordID,
        userID: userID, language: gameLanguage, mode: "2"})
        .done(function(obj, textStatus) {
            console.log(textStatus);
            console.log(obj);
            getGameScore();
        })
        .fail(function() {
            console.log("Submitting translation failed");
        });
}

function saveSettings() {
    saveMenuLanguage("menuLanguageSettings");

    whenToNotify = $("#notifications option:selected").index();
    whenToPost = $("#posts option:selected").index();
    gameLanguage = $("#gamelanguage").val();
    siteLanguage = $("#menuLanguageSettings option:selected").val();

    var json_data = {userID: userID, notify: whenToNotify, post: whenToPost, gameLanguage: gameLanguage};
    $.get("php/save_settings.php", json_data)
        .done(function(data) {
            console.log("Settings saved");
            location.reload();
        })
        .fail(function(jqXHR, textStatus) {
            console.log("Saving settings failed: " + textStatus);
        });
}

function saveMenuLanguage(whichSlider) {
    siteLanguage = $("#"+whichSlider+" option:selected").val();

    $.getJSON("php/save_menu_language.php", {userID: userID, menuLanguage: siteLanguage})
        .done(function(result, textStatus) {
            console.log(textStatus);
            console.log(result);
        })
        .fail(function() {
            console.log("Failed to save menuLanguage");
        });
}

function postTimeline() {
    $.getJSON("php/post_timeline.php", {userID: userID})
        .done(function(result, textStatus) {
            console.log(textStatus);
            console.log("# of new definitions from user : " + result);
            if(result === 0){
                console.log("No activity to post");
            } else  {
                publishStory(result);
            }
        })
        .fail(function(jqXHR, textStatus) {
            console.log("Posting timeline failed: " + textStatus);
        });
}

function triggerNotification() {
    $.get("php/notification_tweet.php", {userID: userID})
        .done(function(result, textStatus) {
            console.log(result);
        })
        .fail(function(jqXHR, textStatus) {
            console.log("Triggering notification failed: " + textStatus);
        });
}

function updateLeaderboard(){
    console.log("In update leaderboard");

    scoreLanguage = $("#scoreLanguage option:selected").index();
    scoreGame = $("#scoreGame option:selected").index();
    scoretimePeriod = $("#scoretimePeriod option:selected").index();
    scoreMetric = $("#scoreMetric option:selected").index();

    var json_data = {
        userID: userID,
        language: scoreLanguage,
        mode: scoreGame,
        metric: scoreMetric,
        period: scoretimePeriod
    };

    $.getJSON("php/get_user_rank.php", json_data)
        .done(function(obj, textStatus) {
            console.log("Leaderboard response : " + obj);
            console.log("This will be the obj");
            console.log(obj);
            var table = document.getElementById("score_table");

            var max = table.rows.length;
            var i;
            for(i = 0; i < max; i++){
                console.log("DELETED : "+ i + "LENGHTH : " + table.rows.length);
                table.deleteRow(0);
            }

            var rowCount;
            var row;
            for(i === 0; i <  obj[0].length; i++) {
                rowCount = table.rows.length;
                row = table.insertRow(rowCount);
                var rowUserID =  obj[1][i].toString();
                console.log("This is the rowCount: " + rowCount);

                if(rowUserID == userID){
                    row.className = "highlightCurrentUser";
                } else {
                    row.className = "otherUsersInTable";
                }
                row.insertCell(0).innerHTML= '<img id="leaderPic1" src="http://graph.facebook.com/' + rowUserID + '/picture" >';
                row.insertCell(1).innerHTML= obj[2][rowUserID];

                row.insertCell(2).innerHTML= obj[0][i];
                row.insertCell(3).innerHTML= rankString + (parseInt(i) + 1); //since index 0 is first rank
            }

            //add the user from before s score if use ris not in top3
            if( obj[3].rank > 4) {
                rowCount = table.rows.length;
                row = table.insertRow(rowCount);
                row.className = "spaceUnder";
                row.insertCell(0).innerHTML="  ";

                addScoreEntry(4,table, obj);
            }

            //add this user s score if he is not in the top3
            if(obj[3].rank > 3) {
                addScoreEntry(3,table, obj);
            }

            if( obj[5].id != "NOPE" ) {
                addScoreEntry(5,table, obj);
            }
        })
        .fail(function(jqXHR, textStatus) {
            console.log("Getting user rank failed: " + textStatus);
        });
}

function addScoreEntry(indexOfArray, table, obj){
    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);
    if(obj[indexOfArray].id == userID){
        row.className = "highlightCurrentUser";
    } else  {
        row.className = "otherUsersInTable";
    }
    row.insertCell(0).innerHTML=  '<img id="leaderPic1" src="http://graph.facebook.com/' + obj[indexOfArray].id + '/picture" >';
    row.insertCell(1).innerHTML= obj[2][obj[indexOfArray].id];

    row.insertCell(2).innerHTML= obj[indexOfArray].score;
    row.insertCell(3).innerHTML= "Rank: " + obj[indexOfArray].rank;
}

function insertGameIcons(gameLanguage) {
    // Remove previous buttons
    $(".btn-game").remove();

    $.getJSON("php/get_games_by_language.php", {languageID: gameLanguage})
        .done(function(games, textStatus) {
            console.log(textStatus);
            console.log(games);

            $.each(games, function(index) {
                var id = games[index].GameID;
                var name = games[index].Name;
                var html = "<img title='" + name + "' id='enter" + id + "'" +
                    "class='btn-game shaded enter' src='media/gamelogos/" + id + ".png' " +
                    "onmousedown='playClick();enter_game(" + id + ");'>";
                $(html).insertAfter("#logo");

            });
            animate_logo();
        })
        .fail(function() {
            console.log("Fetching games for language " + gameLanguage + " failed");
        });
}

function getGameNames() {
    $.getJSON("php/get_games.php")
        .done(function(games, textStatus) {
        })
        .fail(function() {
            console.log("Getting games failed");
        });
}

function getActiveGameLanguages() {
    $.getJSON("php/get_active_languages.php", {userID: userID})
        .done(function(languages, textStatus) {
            set_game_languages(languages);
        })
        .fail(function(jqXHR, textStatus) {
            console.log("Getting active game language failed: " + textStatus);
        });
}

function getInterfaceLanguages() {
    $.getJSON("php/get_interface_languages.php", {userID: userID})
        .done(function(languages, textStatus) {
            set_interface_languages(languages);
        })
        .fail(function(jqXHR, textStatus) {
            console.log("Getting interface language failed: " + textStatus);
        });
}
