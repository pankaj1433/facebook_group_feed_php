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
try {
    if(isset($_SESSION['facebook_access_token'])){
        $accessToken = $_SESSION['facebook_access_token'];
    }else{
          $accessToken = $helper->getAccessToken();
    }
  } catch(FacebookResponseException $e) {
     echo 'Graph returned an error: ' . $e->getMessage();
      exit;
  } catch(FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
  }

 

    // $long_live_token = $fb->request('GET', '/oauth/access_token?  
    // grant_type=fb_exchange_token&  
    // client_id='.$appId.'&
    // client_secret='.$appSecret.'&
    // fb_exchange_token='.$_SESSION['facebook_access_token']);

//     var_dump($long_live_token);
//     echo('hellooooooooooo');
// $fb->setDefaultAccessToken($long_live_token);

$response = $fb->request('GET', '/143498529659602/feed');

echo '<pre>';
var_dump($response);
echo '</pre>';


?>