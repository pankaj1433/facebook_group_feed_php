<?php

require_once __DIR__ . '/vendor/autoload.php';
$fb = new Facebook\Facebook([
  'app_id' => '992320760924250',
  'app_secret' => '116450a608505a304b04c7a3eeb9334e',
  'default_graph_version' => 'v2.11',
  ]);

// Since all the requests will be sent on behalf of the same user,
// we'll set the default fallback access token here.
$fb->setDefaultAccessToken('user-access-token');

$requestUserName = $fb->request('GET', '/143498529659602/feed');
$json_string = json_encode($requestUserName, JSON_PRETTY_PRINT);
echo $json_string;