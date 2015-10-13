var min_length = 4;

var default_value = '✎' + ICanWrite;
var translation_default_value = '✎ ' + ICanTranslate;

var autoUpdateIntervalJobID

function InlineEditorController($scope){
    $scope.showtooltip = false;
    $scope.value = default_value;

    $scope.hideTooltip = function(){
        $scope.showtooltip = false;
        if ($scope.value == '') {
            $scope.value = default_value;
            $("#user_definition").attr("class", "inactive_definition");
        }
    }

    $scope.toggleTooltip = function(e){
        e.stopPropagation();

        $scope.showtooltip = !$scope.showtooltip;

        if($scope.showtooltip) {
            remove_active();
            $("#user_definition").attr("class", "active_definition");
            if($scope.value == default_value) {
                $scope.value = '';
            }
            console.debug($("#input_tool_box"));
            setTimeout( function() {
                $("#input_tool_box").focus();
            }, 20 )
            //document.getElementById("input_tool_box").select();
        }

    }

    $scope.clear = function(e) {
        e.stopPropagation();
        $scope.value = default_value;
    }

    $scope.searchEnter = function(e) {
        if (e.keyCode == 13) {
            $scope.submitGame1();
        }
    }

    $scope.submitGame1 = function() {
        $scope.hideTooltip();
        playClick();vote();
        get_ranked();
        $scope.value = '';
        getGameScore();
    }
}

//THIS IS REALLY UGLY - TO BE REWORKED
function InlineEditorController2($scope){
    console.log("in this thing");

    $scope.showtooltip2 = false;
    $scope.translation = translation_default_value;

    $scope.hideTooltip2 = function(){
        $scope.showtooltip2 = false;
        if ($scope.translation == '') {
            $scope.translation = translation_default_value;
            $("#user_translation").attr("class", "inactive_definition");
        }
    }

    $scope.toggleTooltip2 = function(e){
        e.stopPropagation();

        $scope.showtooltip2 = !$scope.showtooltip2;

        if($scope.showtooltip2) {
            remove_active_translations();
            $("#user_translation").attr("class", "active_definition");
            if($scope.translation == translation_default_value) {
                $scope.translation = '';
            }
            setTimeout( function() {
                $("#translation_input_tool_box").focus();
            }, 20 )
        }
    }

    $scope.clear2 = function(e) {
        e.stopPropagation();
        $scope.translation = translation_default_value;
    }

    $scope.searchEnter2 = function(e) {
        if (e.keyCode == 13) {
            $scope.submitGame2();
        }
    }

    $scope.submitGame2 = function() {
        soumettre_traduction();
        get_ranked_mode_2();
        getGameScore();
        $scope.hideTooltip2();
        playClick();
        $scope.translation = translation_default_value;
    }
}

function enter_game1() {
    pause_animation();
    game = 1;
    getGameScore();

    get_ranked();
    $("#instructions1").html(writeOrVote + gameLanguages[gameLanguage]);
    $("#welcome").css("display", "none");
    $("#game").css("display", "inline-block");
    $("#gamezone1").css("display", "inline-block");
    $("#gamezone2").css("display", "none");
    $("#gamezone3").css("display", "none");
    $("#gamezone4").css("display", "none");

    // $("#gamezone-main1").css("display", "inline-block");
    // $("#gamezone-main2").css("display", "none");
    // $("#footer-next1").css("display", "inline-block");
    // $("#footer-next2").css("display", "none");

}

function enter_game2() {
    pause_animation();
    game = 2;
    getGameScore();
    get_ranked_mode_2();
    $("#instructions2").html(translateTheFollowing + gameLanguages[gameLanguage]);
    $("#welcome").css("display", "none");
    $("#game").css("display", "inline-block");
    $("#gamezone1").css("display", "none");
    $("#gamezone2").css("display", "inline-block");
    $("#gamezone3").css("display", "none");
    $("#gamezone4").css("display", "none");

}

function enter_game3() {
    pause_animation();

    game = 3;
    getGameScore();
    getRankedForTweets();
    $("#welcome").css("display", "none");
    $("#game").css("display", "inline-block");
    $("#gamezone1").css("display", "none");
    $("#gamezone2").css("display", "none");
    $("#gamezone3").css("display", "inline-block");
    $("#gamezone4").css("display", "none");
}

function enter_game4() {
    game = 4;
    getGameScore();
    $("#welcome").css("display", "none");
    $("#game").css("display", "inline-block");
    $("#gamezone1").css("display", "none");
    $("#gamezone2").css("display", "none");
    $("#gamezone3").css("display", "none");
    $("#gamezone4").css("display", "inline-block");

    pause_animation();
    getRankedForSwahili();
}

function display_settings() {
    $("#welcome").css("display", "none");
    $("#profile").css("display", "none");
    $("#settings").css("display", "inline-block");
}

function display_leaderboard() {
    $("#profile").css("display", "none");
    $("#leaderboard").css("display", "inline-block");
}

function display_info1(){
    console.log("Displaying info 1");
    $("#game").css("display", "none");
    $("#info1").css("display", "inline-block");
}
function display_info2(){
    console.log("Displaying info 1");
    $("#game").css("display", "none");
    $("#info2").css("display", "inline-block");
}
function display_info3(){
    console.log("Displaying info 1");
    $("#game").css("display", "none");
    $("#info3").css("display", "inline-block");
}

function display_about() {
    $("#game").css("display", "none");
    $("#about").css("display", "inline-block");
}

function display_profile() {
    getGameScore();
    stopAutoUpdateOfLeaderboard();
    $("#settings").css("display", "none");
    $("#leaderboard").css("display", "none");
    $("#changeMenuLanguage").css("display", "none");
    $("#welcome").css("display", "none");
    $("#game").css("display", "none");

    $("#profile").css("display", "inline-block");
    $("#yourachievements").html(yourAchievements + gameNames[game] + stringin + gameLanguages[gameLanguage]);
}

function display_changeLanguage() {
    $("#welcome").css("display", "none");
    $("#profile").css("display", "none");
    $("#settings").css("display", "none");
    $("#changeMenuLanguage").css("display", "inline-block");
}

function return_to_game() {
    $("#changeMenuLanguage").css("display", "none");
    $("#settings").css("display", "none");

    $("#about").css("display", "none");
    $("#profile").css("display", "none");
    $("#game").css("display", "inline-block");

    $("#info1").css("display", "none");
    $("#info2").css("display", "none");
    $("#info3").css("display", "none");
}

function display_welcome() {
    $("#game").css("display", "none");
    $("#welcome").css("display", "inline-block");
    $("#leaderboard").css("display", "none");
    $("#changeMenuLanguage").css("display", "none");
    //display only the games that are avilable in the currentlanguage

    $.getJSON("php/get_games_by_language.php", {languageID: gameLanguage})
        .done(function(games, status) {
            console.log(status);
            console.log(data);

            $.each(games, function(index) {
                var id = games[index].GameID;
                var name = games[index].Name;
                var html = "<img title='" + name + "' id='game" + id + "'"
                    + "class='shaded-enter' src='media/gamelogos/" + id + ".png' "
                    + "onmousedown='playClick();enter_game" + id + "();'>";
                $("#logo").insertAfter(html);
            });
        })
        .fail(function() {
            console.log("Fetching games for language " + gameLanguage + " failed");
        });


/*
    console.log(implementedGames)
        console.log(gameLanguage)
        for( i = 1; i < 5; i++){
            if(  $.inArray(gameLanguage, implementedGames[i]) == -1) {
                $("#enter"+i).css("display", "none");
            }
            else {
                $("#enter"+i).css("display", "inline-block");
            }
    }
*/

    continue_animation();
}

function animate_logo() {

    $("#logo").addClass("animatelogo");
    $("#enter1").removeClass("shaded_enter");
    $("#enter1").addClass("animateenter");
    $("#enter2").removeClass("shaded_enter");
    $("#enter2").addClass("animateenter");
    $("#enter3").removeClass("shaded_enter");
    $("#enter3").addClass("animateenter");
    $("#enter4").removeClass("shaded_enter");
    $("#enter4").addClass("animateenter");

}

function changeColorOnClick(tweetDisplay,newInput){
    if(newInput.checked){
        tweetDisplay.style.color = "blue";
    }
    else {
        tweetDisplay.style.color = "#af0800";
    }
}

function animate_logo_firstTime(){
    $("#logo").addClass("animatelogo");
    $("#enter0").removeClass("shaded_enter");
    $("#enter0").addClass("animateenter");
}

function animate_logo_login(){
    $("#logo").addClass("animatelogo");
    $("#enterLogin").removeClass("shaded_enterLogin");
    $("#enterLogin").addClass("animateenterLogin");
}

function set_consensus(definition) {
    $("#consensus").html(generalSense + "<strong>" + definition + "</strong>");
}

function set_word(word, pos) {
    $("#word").html(word);
    $("#pos").html(pos);
}

function set_avatar() {
    $("#avatar").attr("src", "https://graph.facebook.com/" + userID + "/picture");
    $("#profile_avatar").attr("src", "https://graph.facebook.com/" + userID + "/picture??width=200&height=200");
}

function set_profile_data(points, pendingPoints, ratio) {
    $("#profile_name").html(userName);
    $("#profile_points").html(points);
    $("#pending_points").html(pendingPoints);
    $("#profile_attempts").html(ratio);
}

function remove_active_translations() {
    $("#translations li").each(function(i, li) {
        $(li).addClass("inactive_definition");
    });
}

function remove_active() {
    $("#definitions li").each(function(i, li) {
        $(li).addClass("inactive_definition");
    });
}

function clear_definitions() {
    var ul = document.getElementById("definitions");
    var li =  ul.getElementsByTagName("li");

    for(var i = li.length - 1; i > 0; i = i - 1) {
        ul.removeChild(li[i]);
    }
}

function add_translation_dunno(definition) {
    var ul = document.getElementById('translations');
    var li = document.createElement("li");
    li.setAttribute("id", "translation_dunno");
    li.classList.add("inactive_definition");
    li.innerHTML = definition;

    li.onmousedown = (function() {
        return function () {
            $("#user_translation").attr("class", 'inactive_definition');
            this.className = "active_definition";
            playClick();
            get_ranked_mode_2();
        };
    })();

    ul.appendChild(li);
}

function add_definition(id, definition, spam) {
    var ul = document.getElementById('definitions');
    var li = document.createElement("li");
    li.classList.add("inactive_definition");
    var div_footer = document.createElement("div");
    div_footer.classList.add("button_div_footer");
    li.innerHTML = definition;
    var img1 = document.createElement("img");
    img1.src = 'media/exclamation.png';
    img1.classList.add('vote_button');
    img1.id = 'vote_button';

    img1.title = "Report spam";
    div_footer.appendChild(img1);

    li.onmousedown = (function(id_num) {
        return function () {

            remove_active();
            this.className = "active_definition";
            definitionID = id_num;
            playClick();
            vote();
            get_ranked();

        };
    })(id);

    img1.onmousedown = (function(id_num) {
        return function () {
            definitionID = id_num;
            report_spam(definitionID);
        };
    })(id);

    //li.appendChild(div_main);
    //li.appendChild(div_footer);
    if(spam){
        li.appendChild(img1);
    }

    ul.appendChild(li);
}

function add_trophy(word, definition) {
    var table = document.getElementById("profile_trophies");
    var row = table.insertRow(0);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    // cell1.classList.add("left_cell");
    // cell2.classList.add("right_cell");
    var img = document.createElement("img");
    img.classList.add('trophy');
    img.src = 'media/medal.png';
    cell1.appendChild(img);
    cell2.innerHTML = word;
    cell3.innerHTML = definition;
}

function vote() {
    var user_definition = document.getElementById("input_tool_box").value;
    if(definitionID != -1) {
        console.log("Submitting Vote for definitionID : " + definitionID);
        submit_vote(definitionID, 1);
    }
    else if(user_definition != default_value && user_definition.length >= min_length) {
        console.log("Submitting new definition : " + user_definition);
        submit_definition(user_definition);
    }
}

function soumettre_traduction() {
    var user_translation = document.getElementById("translation_input_tool_box").value;
    var class_name = document.getElementById("user_translation").className;
    //  if(class_name == "active_definition" && user_translation != translation_default_value) {
    submit_translation(user_translation);
    //  }
}

function startAutoUpdateOfLeaderboard() {
    languageSelect = document.getElementById("scoreLanguage");
    scoreLanguage = languageSelect.selectedIndex;
    gameSelect = document.getElementById("scoreGame");
    scoreGame= gameSelect.selectedIndex;
    timePeriodSelect = document.getElementById("scoretimePeriod");
    scoretimePeriod = timePeriodSelect.selectedIndex;
    metricSelect = document.getElementById("scoreMetric");
    scoreMetric = metricSelect.selectedIndex;

    var whichSliderToChange = 0;

    autoUpdateIntervalJobID= setInterval(function () {
        if(document.getElementById("autoloop").checked) {
            var whatTochange = languageSelect;

            switch(whichSliderToChange) {
                case 0:
                    whatTochange = languageSelect;
                    break;
                case 1:
                    whatTochange = gameSelect;
                    break;
                case 2:
                    whatTochange = timePeriodSelect;
                    break;
                case 3:
                    whatTochange = metricSelect;
                    break;
                default:
                    console.log("PEROGVJEöRKFJ")
                        break;
            }
            whatTochange.selectedIndex = (whatTochange.selectedIndex + 1)  % (whatTochange.length) ;
            whichSliderToChange= (whichSliderToChange +1) % 4;
            console.log("INTERBVAAAAAAAAAAL" + whichSliderToChange)
                updateLeaderboard();
        }

    }, 8000);

}

function stopAutoUpdateOfLeaderboard() {
    clearInterval(autoUpdateIntervalJobID);
}

function updatePermanentMetrics(points, pendingPoints){
    console.debug("Udpating permanent metrics: " + points + pendingPoints);
    $("#points-pending").html(pointsInPlay + pendingPoints)
    $("#points-total").html(pointsBanked + points)
}
