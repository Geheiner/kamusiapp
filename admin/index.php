<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

$fbat = $_SESSION['facebook_access_token'];
if(!isset($fbat)) {
    header('Location:php/login.php');
    exit();
} else {
    $fb = new Facebook\Facebook([
        'app_id' => $app_id,
        'app_secret' => $app_secret,
        'default_graph_version' => 'v2.2',
    ]);
    $oAuth2Client = $fb->getOAuth2Client();

    $tokenMetadata = $oAuth2Client->debugToken($fbat);
    $tokenMetadata->validateExpiration();

    try {
        // Ask Facebook if Token is valid
        $validation = $fb->get("/debug_token?input_token=$fbat&access_token=$app_id|$app_secret");
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned n error: ' . $e->getMessage();
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }

    // Check response
    if($validation->getDecodedBody()['data']['is_valid']) {
        // token is valid
        // create internal API token
        $api_token = bin2hex(openssl_random_pseudo_bytes(16));
        $_SESSION['api_token'] = $api_token;
    } else {
        // token invalid
        unset($_SESSION['facebook_access_token']);
        header('Location:php/login.php');
        exit();
    }
}
?>

<html>
    <head>
        <title>Kamusi Game Backend</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
    </head>
    <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="js/server_requests.js"></script>
    <script src="../js/login.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script>
        var api_token = "<?php echo $api_token; ?>";
    </script>
    <h1>Admin Panel</h1>
    <ul class="menu">
        <li><a href="#" onclick="loadGameLanguages()">Manage Games & Languages</a></li>
        <li><a href="#" onclick="loadInterfaceLanguages()">Manage Interface Languages</a></li>
    </ul>
    <div id="settings">
    </div>
</table>
