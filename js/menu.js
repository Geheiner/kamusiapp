// Minimal length of a definition
var min_length = 4;

var default_value = '✎' + ICanWrite;
var translation_default_value = '✎ ' + ICanTranslate;

var autoUpdateIntervalJobID;

// Define application components
var div_welcome = $("#welcome");
var div_game = $("#game");
var div_gamezone1 = $("#gamezone1");
var div_gamezone2 = $("#gamezone2");
var div_gamezone3 = $("#gamezone3");
var div_gamezone4 = $("#gamezone4");
var div_profile = $("#profile");
var div_info1 = $("#info1");
var div_info2 = $("#info2");
var div_info3 = $("#info3");
var div_info4 = $("#info4");
var div_settings = $("#settings");
var div_about = $("#about");
var div_instructions1 = $("#instructions1");
var div_instructions2 = $("#instructions2");
var div_leaderboard = $("#leaderboard");
var div_changeMenuLanguage = $("#changeMenuLanguage");

// Create array with all views
var all_views = [
    div_welcome,
    div_game,
    div_profile,
    div_info1,
    div_info2,
    div_info3,
    div_info4,
    div_settings,
    div_about,
    div_instructions1,
    div_instructions2,
    div_leaderboard,
    div_changeMenuLanguage
];

// Create array with gamezones
var games = [
    div_gamezone1,
    div_gamezone2,
    div_gamezone3,
    div_gamezone4,
];

/*
 * This function hides all views and then displays the requested views.
 */
function show_views(view_to_show) {
    $.each(all_views, function(index, view) {
        view.css("display", "none");
    });
    view_to_show.css("display", "inline-block");
}

/*
 * This function displays the game specified in the parameter
 */
function activate_game(game_to_activate) {
    $.each(games, function(index, game) {
        game.css("display", "none");
    });
    $("#"+game_to_activate).css("display", "inline-block");
}

function InlineEditorController($scope){
    $scope.showtooltip = false;
    $scope.value = default_value;

    $scope.hideTooltip = function(){
        $scope.showtooltip = false;
        if ($scope.value === '') {
            $scope.value = default_value;
            $("#user_definition").attr("class", "inactive_definition");
        }
    };

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
            }, 20 );
        }

    };

    $scope.clear = function(e) {
        e.stopPropagation();
        $scope.value = default_value;
    };

    $scope.searchEnter = function(e) {
        if (e.keyCode == 13) {
            $scope.submitGame1();
        }
    };

    $scope.submitGame1 = function() {
        $scope.hideTooltip();
        playClick();vote();
        getRanked(1);
        $scope.value = '';
        getGameScore();
    };
}

//THIS IS REALLY UGLY - TO BE REWORKED
function InlineEditorController2($scope){
    $scope.showtooltip2 = false;
    $scope.translation = translation_default_value;

    $scope.hideTooltip2 = function(){
        $scope.showtooltip2 = false;
        if ($scope.translation === '') {
            $scope.translation = translation_default_value;
            $("#user_translation").attr("class", "inactive_definition");
        }
    };

    $scope.toggleTooltip2 = function(e){
        e.stopPropagation();

        $scope.showtooltip2 = !$scope.showtooltip2;

        if($scope.showtooltip2) {
            $("#translations li").each(function(i, li) {
                $(li).addClass("inactive_definition");
            });
            $("#user_translation").attr("class", "active_definition");
            if($scope.translation == translation_default_value) {
                $scope.translation = '';
            }
            setTimeout( function() {
                $("#translation_input_tool_box").focus();
            }, 20 );
        }
    };

    $scope.clear2 = function(e) {
        e.stopPropagation();
        $scope.translation = translation_default_value;
    };

    $scope.searchEnter2 = function(e) {
        if (e.keyCode == 13) {
            $scope.submitGame2();
        }
    };

    $scope.submitGame2 = function() {
        soumettre_traduction();
        getRanked(2);
        getGameScore();
        $scope.hideTooltip2();
        playClick();
        $scope.translation = translation_default_value;
    };
}

function enter_game(gameId) {
    pause_animation();
    game = gameId;

    getRanked(gameId);
    getGameScore();

    $("#instructions"+gameId).html(writeOrVote + gameLanguageName);
    show_views(div_game);
    activate_game("gamezone"+gameId);
}

function display_settings() {
    // fetch list of available game languages
    getActiveGameLanguages();
    getInterfaceLanguages();

    show_views(div_settings);
}

function display_leaderboard() {
    show_views(div_leaderboard);
}

function display_info1(){
    console.log("Displaying info 1");
    show_views(div_info1);
}

function display_info2(){
    console.log("Displaying info 1");
    show_views(div_info2);
}

function display_info3(){
    console.log("Displaying info 1");
    show_views(div_info3);
}

function display_about() {
    show_views(div_about);
}

function display_profile() {
    getGameScore();
    stopAutoUpdateOfLeaderboard();

    show_views(div_profile);
    $("#yourachievements").html(yourAchievements + gameNames[game] + stringin + gameLanguageName);
}

function display_changeLanguage() {
    getInterfaceLanguages();
    show_views(div_changeMenuLanguage);
}

function return_to_game() {
    show_views(div_game);
}
function display_welcome() {
    // gets games that are available in current language
    // and adds their icons to the welcome screen
    insertGameIcons(gameLanguage);
    show_views(div_welcome);
    continue_animation();
}

function animate_logo() {
    $("#logo").addClass("animatelogo");
    $(".enter").removeClass("shaded");
    $(".enter").addClass("animate");
}

function changeColorOnClick(tweetDisplay, newInput){
    if(newInput.checked){
        tweetDisplay.style.color = "blue";
    } else {
        tweetDisplay.style.color = "#af0800";
    }
}

function animate_logo_login(){
    $("#logo").addClass("animatelogo");
    $("#enterLogin").removeClass("shaded_enterLogin");
    $("#enterLogin").addClass("animateenterLogin");
}

function set_consensus(definition) {
    $("#consensus").html(generalSense + "<strong>" + definition + "</strong>");
}

// populate the game language setttings list
function set_game_languages(languages) {
    $.each(languages, function(index, value) {
        $("#gamelanguage").append($("<option>").text(value.Ref_Name)
                .attr("value", value.LanguageID));

        // If a gamelanguage has been retrieved we set
        // the dropdown ot that value
        if(value.gamelanguage !== null) {
            $("#gamelanguage").val(value.gamelanguage);
        }
    });
}

// populate the interface language settings list
function set_interface_languages(languages) {
    $.each(languages, function(index, value) {
        $(".interface-lang").append($("<option>").text(value.Ref_Name)
                .attr("value", value.LanguageID));

        // If a interfacelanguage has been retrieved we set
        // the dropdown ot that value
        if(value.interfacelanguage !== null) {
            $(".interface-lang").val(value.interfacelanguage);
        }
    });
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
            getRanked(2);
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
            getRanked(1);

        };
    })(id);

    img1.onmousedown = (function(id_num) {
        return function () {
            definitionID = id_num;
            reportSpam(definitionID);
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
    var user_definition = $("#input_tool_box").val();
    if(definitionID != -1) {
        console.log("Submitting Vote for definitionID : " + definitionID);
        submitVote(definitionID, 1);
    }
    else if(user_definition != default_value && user_definition.length >= min_length) {
        console.log("Submitting new definition : " + user_definition);
        submitDefinition(user_definition);
    }
}

function soumettre_traduction() {
    var user_translation = $("#translation_input_tool_box").val();
    var class_name = document.getElementById("user_translation").className;
    //  if(class_name == "active_definition" && user_translation != translation_default_value) {
    submitTranslation(user_translation);
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

    autoUpdateIntervalJobID = setInterval(function () {
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
                    console.log("Wrong slider");
                    break;
            }
            whatTochange.selectedIndex = (whatTochange.selectedIndex + 1)  % (whatTochange.length) ;
            whichSliderToChange= (whichSliderToChange +1) % 4;
            console.log("Interval " + whichSliderToChange);
            updateLeaderboard();
        }

    }, 8000);

}

function stopAutoUpdateOfLeaderboard() {
    clearInterval(autoUpdateIntervalJobID);
}

function updatePermanentMetrics(points, pendingPoints){
    console.debug("Udpating permanent metrics: " + points + pendingPoints);
    $("#points-pending").html(pointsInPlay + pendingPoints);
    $("#points-total").html(pointsBanked + points);
}
