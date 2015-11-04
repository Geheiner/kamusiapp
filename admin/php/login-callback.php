<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';

$fb = new Facebook\Facebook([
    'app_id' => $app_id,
    'app_secret' => $app_secret,
    'default_graph_version' => 'v2.2',
]);

$helper = $fb->getRedirectLoginHelper();
try {
    $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}

if (isset($accessToken)) {
    // Logged in!
    $_SESSION['facebook_access_token'] = (string) $accessToken;

    try {
        // Set app token for app request
        $fb->setDefaultAccessToken($app_id."|".$app_secret);
        $roles = $fb->get('/'.$app_id.'/roles');

        // Set access token for request
        $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
        $userID = $fb->get('/me')->getGraphUser()->getId();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned n error: ' . $e->getMessage();
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }

    $roles = $roles->getDecodedBody()['data'];

    foreach($roles as $role) {
        if($role['user'] == $userID) {
            header('Location:../index.php');
            exit;
        }
    }
    unset($_SESSION['facebook_access_token']);
    echo "Unauthorized Access.";
}
