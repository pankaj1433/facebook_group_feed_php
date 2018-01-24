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

$helper = $fb->getRedirectLoginHelper();

//get access token


 

    $long_live_token = $fb->request('GET', '/oauth/access_token?  
    grant_type=fb_exchange_token&  
    client_id=145746932777961&
    client_secret=99c46fda46782a33b98e2e3fd12c3c6f&
    fb_exchange_token=EAACEjlonwZBkBABSxz1oIDerJumY7uz0PXbDyZC1dVdtUIZAijY5sk74TDIsjkpZAFgscNZCVg7NzwwG2ZAbTcpB2KGQttON4352tQuzVxCLmi4XjOlOUEvRs5jtibftRsgyJsEYZC8tswfDbygL9C7hPxe9e5mgvBxd9XnvsyjVgzckF7zQQcwIVywRWb2A6oZD');

//     var_dump($long_live_token);
//     echo('hellooooooooooo');
$fb->setDefaultAccessToken($long_live_token);

$response = $fb->request('GET', '/143498529659602/feed');

echo '<pre>';
var_dump($response);
echo '</pre>';


?>