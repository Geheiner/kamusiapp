<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';

$fb = new Facebook\Facebook([
  'app_id' => $app_id,
  'app_secret' => $app_secret,
  'default_graph_version' => 'v2.2',
  ]);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email'];
$callbackUrl = "http://$_SERVER[HTTP_HOST]/admin/php/login-callback.php";
$loginUrl = $helper->getLoginUrl($callbackUrl, $permissions);

echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
?>
