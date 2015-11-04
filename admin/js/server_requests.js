/*
 * Displays a table to see which games are active for which languages
 */
function loadGameLanguages() {
    $.getJSON("php/get_game_languages.php", {token: api_token})
        .done(function(results_array) {
            var languageIdMap = results_array.languageIdMap;
            var gameIdMap = results_array.gameIdMap;
            var gameLanguageActive = results_array.gameLanguageActive;

            // fill header with games
            var html = "<table><tr><th>ISO</th><th>Language Name</th>";
            for (var key in gameIdMap) {
                html += "<th>";
                html += gameIdMap[key];
                html += "</th>";
            }
            html += "</tr>";
            html+= "<tr><td></td>";
            html+= "<td class='langentry'>";
            html+= "<input type='text' id='newlang' onkeyup='lang_autocomplete()'>";
            html+= "<input type='hidden' id='langId' value=''>";
            html+= "</td>";
            for (var game in gameIdMap) {
                html += "<td>";
                html += "<input type='checkbox' class='new' value='"+game+"'>";
                html += "</td>";
            }
            html += "<td><input type='button' value='Add language' onclick='add_language()'></td>";
            html += "</tr>";

            // loop through all languages to display active state
            for (var lang in languageIdMap) {
                html += "<tr><td>" + lang + "</td>";
                html += "<td class='langentry'>";
                html += languageIdMap[lang];
                html += "</td>";
                for (var game in gameIdMap) {
                    html += "<td>";
                    if(gameLanguageActive.hasOwnProperty(lang)) {
                        var checked = "";
                        if(gameLanguageActive[lang].hasOwnProperty(game)) {
                            if(gameLanguageActive[lang][game] == 1) {
                                checked = " checked";
                            }
                        }
                        html += "<input class='toggle_active' value='" + game + "|" + lang +
                            "' type='checkbox'" + checked + ">";
                    }
                    html += "</td>";
                }
                html += "<td><input type='button' value='delete' onclick='delete_game_language(\"" + lang + "\")'></td>";
                html += "</tr>";
            }
            html += "</table>";
            $("#settings").html(html);
        });
}

function loadInterfaceLanguages() {
    $.getJSON("php/get_interface_languages.php", {token: api_token})
        .done(function(languages, textStatus) {
            var html = "<table>";
            html += "<tr><td></td>";
            html += "<td class='langentry'>";
            html += "<input type='text' id='newlang' onkeyup='lang_autocomplete()'>";
            html += "<input type='hidden' id='langId' value=''>";
            html += "</td></tr>";
            for(var lang in languages) {
                html += "<tr><td>" + lang + "</td>";
                html += "<td>" + languages[lang] + "</td>";
                html += "</tr>";
            }
            html += "</table>";
            $("#settings").html(html);
        })
        .fail(function(jqXHR, textStatus) {
            console.log("Loading interface languages failed: " + textStatus);
        });
}

/*
 * Gets languages from the ISO list based on input
 */
function lang_autocomplete() {
    $("#newlang").autocomplete({
        minLength: 2,
        // get data
        source: function(request, response) {
            $.getJSON("php/autocomplete_language.php", {lang: $("#newlang").val(), token: api_token})
                .done(function(data) {
                    response($.map(data, function(obj) {
                        return {
                            label: obj.Id + " - " + obj.Ref_Name,
                            value: obj.Ref_Name,
                            id: obj.Id
                        }
                    }));
                });
        },
        // write language id to hidden field so db entry can be written later
        select: function(event, ui) {
            $("#langId").val(ui.item.id);
        }
    });
}

function add_language() {
    // Collect all checked boxes
    var active_boxes = $.map($(".new"), function(item, index) {
        if($(item).is(":checked")) {
            return $(item).val();
        }
    });

    // Get id of language to add
    var language = $("#langId").val();

    $.post("php/add_game_language.php", {language: language, games: JSON.stringify(active_boxes), token: api_token})
        .done(function(result, textStatus) {
            // refresh screen
            loadGameLanguages();
        })
        .fail(function(jqXHR, textStatus) {
            console.log(textStatus);
        });
}

/*
 * Delete a game language (for all games)
 */
function delete_game_language(lang) {
    var confirmation = confirm("Are you sure that you want to delete this language:\n" +lang);
    if(!confirmation) {
        return false;
    }
    $.post("php/delete_language.php", {lang: lang, token: api_token})
        .done(function(result, textStatus) {
            console.log(result);
            console.log(textStatus);
            loadGameLanguages();
        })
        .fail(function(jqXHR, textStatus) {
            console.log("Deleting game language failed");
        });
}

/*
 * Register toggle active handler to checkboxes of languages that already exist
 */
$(document).on("change", ".toggle_active", function() {
    var value = $(this).val().split("|");
    var game = value[0];
    var lang = value[1];
    toggle_active(lang, game, this);
});

function toggle_active(lang, game, checkbox) {
    var box = $(checkbox);
    var is_active = box.is(":checked");
    var parent_cell = box.parent();

    // Set transition for visual feedback of saving
    parent_cell.css('transition', 'background-color 0.1s linear');
    function fadeout() {
        setTimeout(function() {
            parent_cell.css('background-color', '');
        }, 200);
    }
    $.post("php/toggle_game_active.php", {lang: lang, game: game, token: api_token, is_active: is_active | 0})
        .done(function(result, textStatus) {
            parent_cell.css('background-color', 'green');
            fadeout();
        })
        .fail(function(jqXHR, textStatus) {
            box.prop("checked", !is_active);
            parent_cell.css('background-color', 'red');
            fadeout();
        });
}

