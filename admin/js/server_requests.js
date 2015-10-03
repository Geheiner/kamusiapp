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

            /*
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
                console.log("Nothing found for this keyword")
                
                updateTweetDB("noTweetFound")
                getRankedForTweets();
            }
            */

        }
    }
    xmlhttp.open("GET","php/get_game_languages.php");

    xmlhttp.send();
}
