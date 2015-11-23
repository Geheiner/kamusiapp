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
            html+= "<input type='text' id='newlang'>";
            html+= "<input type='hidden' id='langId' value=''>";
            html+= "</td>";

            var game;
            for (game in gameIdMap) {
                html += "<td>";
                html += "<input type='checkbox' class='new' value='"+game+"'>";
                html += "</td>";
            }
            html += "<td><input type='button' value='Add language' id='addlang'></td>";
            html += "</tr>";

            // loop through all languages to display active state
            for (var lang in languageIdMap) {
                html += "<tr><td>" + lang + "</td>";
                html += "<td class='langentry'>";
                html += languageIdMap[lang];
                html += "</td>";
                for (game in gameIdMap) {
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
                html += "<td><input type='button' value='delete' class='delete gamelanguage' name='" + lang + "'></td>";
                html += "</tr>";
            }
            html += "</table>";
            $("#settings").html(html);
        });
}

function loadInterfaceLanguages() {
    $.getJSON("php/get_interface_languages.php", {token: api_token})
        .done(function(languages, textStatus) {
            // Create table
            var table = $('<table/>');

            // Create table header
            var header = $('<tr/>').
                append($('<th/>').text('ISO')).
                append($('<th/>').text('Language Name')).
                append($('<th/>').text('Locale')).
                append($('<th/>'));

            // Create row for new language
            var newlang = $('<tr/>').
                append($('<td/>')).
                append($('<td/>').
                    append($('<input>').
                        attr({
                            type: 'hidden',
                            id: 'langId',
                            value: ''
                        })
                    ).
                    append($('<input>').
                        attr({
                            type: 'text',
                            id: 'newlang'
                        })
                    )
                ).
                append($('<td/>').
                    append($('<select/>').
                        attr({
                            id: 'newlocale',
                            class: 'locales'
                        })
                    )
                ).
                append($('<td/>').
                    append($('<input/>').
                        attr({
                            type: 'button',
                            value: 'Add language',
                            id: 'addilang'
                        })
                    ));

            // Compose Table
            table = table.
                append(header).
                append(newlang);

            // Add interface language entries from DB to table
            $.each(languages, function(index, language) {
                table.
                    append($('<tr/>').
                        append($('<td/>').text(index)).
                        append($('<td/>').text(language.name)).
                        append($('<td/>').
                            append($('<select/>').
                                attr({
                                    class: 'locales',
                                    name: index,
                                    id:language.locale
                                })
                            )
                        ).
                        append($('<td/>').
                            append($('<input/>').
                                attr({
                                    type: 'button',
                                    value: 'delete',
                                    name: index,
                                    class: 'delete interfacelanguage'
                                })
                            )
                        )
                    );
            });

            // Attach table
            $('#settings').html(table);

            // Append locales
            append_locales();
        })
        .fail(function(jqXHR, textStatus) {
            console.log("Loading interface languages failed: " + textStatus);
        });

    function append_locales() {
        $.getJSON("php/get_system_locales.php", {token: api_token})
            .done(function(locales, textStatus) {
                // Add default option en_US
                $('.locales').append($('<option>', {value: "en_US"})
                    .text("en_US"));

                // Add options for which translations exist
                for(var locale in locales) {
                    loc = locales[locale];
                    $('.locales').each(function(index) {
                        $(this).append($('<option>', {value: loc}).
                            text(loc).
                            // select current locale
                            prop('selected', loc==$(this).attr('id'))
                        );
                    });
                }
            })
            .fail(function(jqXHR, textStatus) {
                console.log("Getting locales failed");
            });
    }
}

/*
 * Set locale for interface language
 */
$(document).on("change", ".locales", function() {
    var lang = $(this).attr('name');
    var locale = $(this).val();
    var cell = $(this).parent();

    $.post("php/set_locale_interface_language.php", {token: api_token, lang: lang, locale: locale})
        .done(function(result, textStatus) {
            // Visual feedback success
            flash(cell, 'green');
        })
        .fail(function(jqXHR, textStatus) {
            // Visual feedback failure
            flash(cell, 'red');
        });
});

/*
 * Gets languages from the ISO list based on input
 */
$(document).on("keyup", "#newlang", function() {
    $("#newlang").autocomplete({
        // mininum chars entered before autocompletion starts
        minLength: 2,
        // get languages from ISO list
        source: function(request, response) {
            $.getJSON("php/autocomplete_language.php", {lang: $("#newlang").val(), token: api_token})
                .done(function(data) {
                    response($.map(data, function(obj) {
                        return {
                            label: obj.Id + " - " + obj.Ref_Name,
                            value: obj.Ref_Name,
                            id: obj.Id
                        };
                    }));
                });
        },
        // write language id to hidden field so db entry can be written later
        select: function(event, ui) {
            $("#langId").val(ui.item.id);
        }
    });
});

/*
 * Add new interface language
 */
$(document).on("click", "#addilang", function() {
    // get id & locale of interface language to add
    var language = $("#langId").val();
    var locale = $("#newlocale").val();

    $.post("php/add_interface_language.php", {token: api_token, language: language, locale: locale})
        .done(function(result, textStatus) {
            loadInterfaceLanguages();
        })
        .fail(function(jqXHR, textStatus) {
            console.log("Adding interface language failed");
        });
});

/*
 * Add new game language
 */
$(document).on("click", "#addlang", function() {
    // Collect all checked boxes
    var active_boxes = $.map($(".new"), function(item, index) {
        if($(item).is(":checked")) {
            return $(item).val();
        }
    });

    // Get id of language to add
    var language = $("#langId").val();

    // send db request to add language
    $.post("php/add_game_language.php", {language: language, games: JSON.stringify(active_boxes), token: api_token})
        .done(function(result, textStatus) {
            // refresh screen
            console.log(result);
            loadGameLanguages();
        })
        .fail(function(jqXHR, textStatus) {
            console.log(textStatus);
        });
});

/*
 * Delete a game language (for all games)
 */
$(document).on("click", ".delete", function() {
    var lang = $(this).attr('name');
    var confirmation = confirm("Are you sure that you want to delete this language:\n" +lang);
    if(!confirmation) {
        return false;
    }
    if($(this).hasClass('gamelanguage')) {
        $.post("php/delete_game_language.php", {lang: lang, token: api_token})
            .done(function(result, textStatus) {
                console.log(result);
                console.log(textStatus);
                loadGameLanguages();
            })
            .fail(function(jqXHR, textStatus) {
                console.log("Deleting game language failed");
            });
    } else if($(this).hasClass('interfacelanguage')) {
        $.post("php/delete_interface_language.php", {lang: lang, token: api_token})
            .done(function(result, textStatus) {
                console.log(result);
                console.log(textStatus);
                loadInterfaceLanguages();
            })
            .fail(function(jqXHR, textStatus) {
                console.log("Deleting game language failed");
            });
    }

});

/*
 * Toggle checkboxes of languages that already exist
 */
$(document).on("change", ".toggle_active", function() {
    var value = $(this).val().split("|");
    var game = value[0];
    var lang = value[1];
    var box = $(this);

    var is_active = box.is(":checked");
    var cell = box.parent();

    // Set transition for visual feedback of saving
    $.post("php/toggle_game_active.php", {lang: lang, game: game, token: api_token, is_active: is_active | 0})
        .done(function(result, textStatus) {
            // Visual feedback success
            flash(cell, 'green');
        })
        .fail(function(jqXHR, textStatus) {
            box.prop("checked", !is_active);
            // Visual feedback failure
            flash(cell, 'red');
        });
});


function flash(cell, color) {
    cell.css('transition', 'background-color 0.1s linear');
    cell.css('background-color', color);
    setTimeout(function() {
        cell.css('background-color', '');
    }, 200);
}
