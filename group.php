<?php

require_once __DIR__ . '/vendor/autoload.php';
session_start();
/*
 * Configuration and setup Facebook SDK
 */
$appId         = '145746932777961'; //Facebook App ID
$appSecret     = '99c46fda46782a33b98e2e3fd12c3c6f'; //Facebook App Secret
$redirectURL   = 'https://facebook-group-test-app.herokuapp.com'; //Callback URL
$fbPermissions = array('email');  //Optional permissions

$fb = new \Facebook\Facebook([
    'app_id' => $appId,
    'app_secret' => $appSecret,
    'default_graph_version' => 'v2.11',
]);

$fb->setDefaultAccessToken('user-access-token');

$response = $fb->request('GET', '/143498529659602/feed');

echo '<pre>';
var_dump($response);
echo '</pre>';


?>