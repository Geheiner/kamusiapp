/*
 * Displays a table to see which games are active for which languages
 */
function loadGameLanguages() {
    $.getJSON("php/get_game_languages.php")
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
                        html += "<input type='checkbox'" + checked + ">";
                    }
                    html += "</td>";
                }
                // extra column for add language button
                html += "<td></td>";
                html += "</tr>";
            }
            html += "</table>";
            $("#settings").html(html);
        });
}

function loadInterfaceLanguages() {
    $.getJSON("php/get_interface_languages.php")
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
            $.getJSON("php/autocomplete_language.php", {lang: $("#newlang").val()})
                .done(function(data) {
                    response($.map(data, function(obj) {
                        return {
                            label: obj.Ref_Name,
                            value: obj.Ref_Name,
                            id: obj.Id
                        }
                    }));
                });
        },
        // write id to hidden field so db entry can be written later
        select: function(event, ui) {
            $("#langId").val(ui.item.id);
        }
    });
}

function add_language() {
    var active_boxes = $.map($(".new"), function(item, index) {
        if($(item).is(":checked")) {
            return $(item).val();
        }
    });

    var language = $("#langId").val();
    console.log("Adding language " + language);
    console.log(active_boxes);

    $.post("php/add_game_language.php", {language: language, games: JSON.stringify(active_boxes)})
        .done(function(result, status) {
            console.log(result);
            loadGameLanguages();
        });
}
