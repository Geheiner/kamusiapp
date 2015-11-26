<?php
// 1) Connect, see if you have a session_id and session_name

function setCurlDefaults($ch, $base_url){
    curl_setopt($ch, CURLOPT_POST, 1);

    //equivalent to cache: false
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,30000);

    //so that we get the data in the data variable in not on stdout 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    //so that we get the data in the data variable in not on stdout 
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
}

function connect($session_name, $session_id, $csrf_token, $base_url) {
    global $kamusiUser;

    $connectHeaders = array();
    if ($csrf_token !== '' || $csrf_token !== 'undefined') {
        $connectHeaders = array('X-CSRF-Token: ' .$csrf_token, 'Cookie: ' .$session_name . "=" . $session_id);

    }

    $ch = curl_init();

    setCurlDefaults($ch,$base_url);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);

    curl_setopt($ch, CURLOPT_URL, $base_url . "/facebook_game_v1/system/connect.json");

    curl_setopt($ch, CURLOPT_HTTPHEADER, $connectHeaders);

    $plainresult =  curl_exec($ch);

    $sentHeaders = curl_getinfo($ch);
//  debugVariable($sentHeaders, "Sent headers in connect");
    $result = tryToparseToJSONElseDie($plainresult);

    if (curl_errno($ch)) { 
        print "Error: " . curl_error($ch); 
    } else {
        // Show me the result
//      debugVariable($result, "JSON response in connect");
        if($result->user->uid == 0){
            return false;
        } else {
            return true;
        }

        //storeUserData($base_url, $result->user->uid, $result->session_name, $result->sessid, $csrf_token);
        curl_close($ch); 
    }
}

//2) If Anonymous == uid<0 or uid=empty, you need to login

function login($sess_user, $sess_pass, $base_url) {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $base_url . "/facebook_game_v1/user/login.json");

    setCurlDefaults($ch,$base_url);

    $data = array();
    $data['name']=$sess_user;
    $data['username'] = $sess_user;
    $data['pass'] = $sess_pass;
    $data['password'] = $sess_pass;

    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $plainresult =  curl_exec($ch); 
    $result = tryToparseToJSONElseDie($plainresult);

    if (curl_errno($ch)) { 
        print "Error: " . curl_error($ch); 
    } else { 

    //  debugVariable($result, "JSON response in login"); 

        storeUserData($base_url, $result->user->uid, $result->session_name, $result->sessid, '');
        curl_close($ch); 
    }
}
//3) Get Token, anonymous token
function getToken($session_name, $session_id, $base_url) {
    global $kamusiUser;

    $ch = curl_init();
    setCurlDefaults($ch,$base_url);

    $headers = array();

    if($session_name != '' && $session_id != '' ){
    //  curl_setopt($ch, CURLOPT_HEADER, TRUE);
        $headers = array ('Cookie: ' .$session_name . "=" . $session_id);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    curl_setopt($ch, CURLOPT_URL, $base_url . "/facebook_game_v1/user/token.json");

    //set requestHEaderBefore Send?
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);

    $plainresult =  curl_exec($ch);

    $sentHeaders = curl_getinfo($ch);
//  debugVariable($sentHeaders, "Sent headers in getToken"); 

    $result = tryToparseToJSONElseDie($plainresult);

    if (curl_errno($ch)) { 
        print "Error: " . curl_error($ch); 
    } else {
//      debugVariable($result, "JSON response in getToken"); 

        $kamusiUser['csrf_token'] = $result->token;

        curl_close($ch); 
    }
}

function authentification(){
    global $kamusiUser, $base_url;
    //We have tried to authenticate before
    if (array_key_exists ('session_name', $kamusiUser ) ){
        //We are not connected even though we have authenticated before
        if (!connect($kamusiUser['session_name'], $kamusiUser['session_id'], $kamusiUser['csrf_token'], $base_url)){
            if(!loginProcess()){
                die("Could not login!");
            } else {
                return true;
            }
        } else {
            return true;
        }
    } else {
        if(!loginProcess()){
            die("Could not login!");
        } else {
            return true;
        }
    }
}

function logout() {
    global $base_url, $kamusiUser;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $base_url . "/facebook_game_v1/user/logout.json");

    $logoutHeaders = array();
    if (array_key_exists ('csrf_token', $kamusiUser)) {
        $logoutHeaders = array('X-CSRF-Token: ' .$kamusiUser['csrf_token'], 'Cookie: ' . $kamusiUser['session_name'] . "=" . $kamusiUser['session_id']);
    } else {
        die("Trying to logout witout having been logged in!");
    }

    setCurlDefaults($ch,$base_url);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $logoutHeaders);

    $plainresult =  curl_exec($ch);
//  debugVariable($plainresult, 'logout ');
    setCurlDefaults($ch,$base_url);

}

function loginProcess(){
    global $base_url, $sess_user, $sess_pass, $kamusiUser;
    getToken('', '', $base_url);
    login($sess_user, $sess_pass, $base_url);
    getToken($kamusiUser['session_name'], $kamusiUser['session_id'], $base_url);
    return  connect($kamusiUser['session_name'], $kamusiUser['session_id'], $kamusiUser['csrf_token'], $base_url);
}

function authenticatedGETRequest($endURL){
    global $kamusiUser, $base_url;

    $ch = curl_init();
    setCurlDefaults($ch,$base_url);
    curl_setopt($ch, CURLOPT_POST, 0);

    curl_setopt($ch, CURLOPT_URL, $base_url . $endURL);

    $authenticatedGETRequestHeaders = array();
    if (array_key_exists ('csrf_token', $kamusiUser)) {
        $authenticatedGETRequest = array('X-CSRF-Token: ' .$kamusiUser['csrf_token'], 'Cookie: ' . $kamusiUser['session_name'] . "=" . $kamusiUser['session_id']);
    } else {
        die("Not logged in for swahili headers");
    }


    curl_setopt($ch, CURLINFO_HEADER_OUT, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $authenticatedGETRequest);

    return curl_exec($ch);

}

function getSwahiliwords($uid){

    $result = getUserPosAndOffset($uid);

//  debugVariable($result, 'USER PSOTIION : ' . $result );

    
    $pageNumber = floor($result / 25);
    $pageEntry = $result % 25;

    $plainResult = null;
    $numberOfTimeITry = 3;
    $nuberOfTries = 0;
    while($nuberOfTries < $numberOfTimeITry && ($plainResult == null || $plainResult == '' || empty($plainResult))) {
    $plainResult = authenticatedGETRequest("/facebook_game_v1/search-define.json?to_language=371&page=2");//.$pageNumber);
    }
    if($plainResult == null){
        die("Services are down!");
    }
//  debugVariable($pageNumber, 'page numebr');

    $json = json_decode($plainResult, true);
//  debugVariable($json[$pageEntry], 'words: ' );

    //What we will return to the javascript

    if(array_key_exists ($pageEntry, $json)){
    echo json_encode($json[$pageEntry]);
    } else {
        debugVariable($pageEntry, 'pahe entry');
        debugVariable($pageNumber, 'page number');

        debugVariable($json, 'json');
        debugVariable($plainResult, 'plainResult');
    }

}

function getUserPosAndOffset($uid){
    global $mysqli;

    $maxOffset = 100;

    //fetch the user in order to see which word is for him
    $stmt = $mysqli->prepare("SELECT * FROM games WHERE userid = ? AND language = 4 AND game= 4 ");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $position = $row["position"];
    $offset = $row["offset"];
    $stmt->close();



    if($offset == $maxOffset){

        $stmt = $mysqli->prepare("UPDATE games SET offset = 0 WHERE userid=? AND language = 4 AND game = 4;");
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        $stmt->close();

        $stmt = $mysqli->prepare("UPDATE games SET position = position +1 WHERE userid=? AND language = 4 AND game = 4;");
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $mysqli->prepare("UPDATE games SET offset = offset +10 WHERE userid=? AND language = 4 AND game = 4;");
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        $stmt->close();
    }
    return $position + $offset;

}

function tryToparseToJSONElseDie($whatToParse){
    $result = json_decode($whatToParse);
    if($result == NULL){
        die("Could not parse CURL response to JSON, what we got was: \n" . $whatToParse);
    }
    return $result;
}

function storeUserData($base_url, $uid, $session_name, $session_id, $csrf_token) {
    global $kamusiUser;
    $kamusiUser['base_url'] = $base_url;
    $kamusiUser['uid'] = $uid;
    $kamusiUser['session_name'] = $session_name;
    $kamusiUser['session_id'] = $session_id;

    if($csrf_token != ''){
        $kamusiUser['csrf_token'] = $csrf_token;
    }
}

