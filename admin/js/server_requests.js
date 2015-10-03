function loadGameLanguages() {
    var xmlhttp;
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            console.log("RESPONSE TEXT : " + xmlhttp.responseText + " END RESPONSE TEXT")
            var results_array = JSON.parse(xmlhttp.responseText);

            var languageIdMap = results_array.languageIdMap;
            var gameIdMap = results_array.gameIdMap;
            var gameLanguageActive = results_array.gameLanguageActive;

            document.getElementById("settings").innerHTML =
                languageIdMap + 
                gameIdMap +
                gameLanguageActive;

        }
    }
    xmlhttp.open("GET","php/get_game_languages.php");

    xmlhttp.send();
}
