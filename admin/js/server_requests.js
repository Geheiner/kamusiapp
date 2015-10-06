/*
 * Displays a table to see which games are active for which languages
 */
function loadGameLanguages() {
    $.get("php/get_game_languages.php")
        .done(function(data) {
            var results_array = JSON.parse(data);

            var languageIdMap = results_array.languageIdMap;
            var gameIdMap = results_array.gameIdMap;
            var gameLanguageActive = results_array.gameLanguageActive;

            // fill header with games
            var html = "<table><tr><td></td>";
            for (var key in gameIdMap) {
                html += "<td>";
                html += gameIdMap[key];
                html += "</td>";
            }
            html += "</tr>";

            // loop through all languages to display active state
            for (var lang in languageIdMap) {
                html += "<tr><td>";
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
                html += "</tr>";
            }

            html+= "<tr>";
            html+= "<td>";
            html+= "<input type='text' id='newlang' onkeyup='lang_autocomplete()'>";
            html+= "<input type='hidden' id='langId' value=''>";
            html+= "</td>";
            for (var game in gameIdMap) {
                html += "<td>";
                html += "<input type='checkbox' id='"+game+"'>";
                html += "</td>";
            }
            html += "</tr>";
            html += "</table>";

            document.getElementById("settings").innerHTML = html;
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
