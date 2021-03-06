// Load and initialise the Facebook API. xfbml checks for active login

var appID;
if(window.location.href == 'http://localhost/') {
    // AppID of Kamusi Test App
    appID = '1672140659691650';
} else {
    // AppID of Kamusi Main App
    appID = '1525612724344445';
}

var firsttime = false;

function checkLoginState() {
    FB.getLoginStatus(function(response) {
        if (response.status === 'connected') {
            $("#login_button").css("display", "none");
            $("#enterLogin").remove();

            FB.api('/me', function(response) {
                console.log(response);
                console.log(response.id);
                userID = response.id;
                console.log(userID);
                userName = response.name;
                isNewUser();
            });
        } else if (response.status === 'not_authorized') {
            $('#word').html('Please log into this app.');
        } else {
            $('#word').html('Please log into Facebook.');
            animate_logo_login();
        }
    });
}

//Called when page loads
window.fbAsyncInit = function() {
    //Initialise SDK
    FB.init({
        appId      : appID,
        cookie     : true,  // enable cookies to allow the server to access 
        // the session
        xfbml      : true,  // parse social plugins on this page
        version    : 'v2.1' // use version 2.1
    });

    //Get login status
    checkLoginState();
};

// Load the SDK asynchronously
(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
} (document, 'script', 'facebook-jssdk')
);

function share() {
    FB.ui({
        method: 'share',
        href: 'https://apps.facebook.com/thekamusiapp/',
    }, function(response){}
    );
}

function request() {
    FB.ui({method: 'apprequests',
        message: 'Kamusi is Swahili for "dictionary"'
    }, function(response){
        console.log(response);
    });
}

function publishStory(text) {
    //text will be the number of achievements, for now the number of validated tweets.
    //We will send out a link containing userID and # of validated tweets
    FB.ui({
        method: 'share',
        href: 'http://ec2-52-11-133-223.us-west-2.compute.amazonaws.com/shareTest.html',
    }, function(response){});

}
