<?php

require_once __DIR__ . '/vendor/autoload.php';

$redirectURL   = 'https://facebook-group-test-app.herokuapp.com';

$fb = new Facebook\Facebook([
  'app_id' => '145746932777961',
  'app_secret' => '99c46fda46782a33b98e2e3fd12c3c6f',
  'default_graph_version' => 'v2.11',
  ]);

// Since all the requests will be sent on behalf of the same user,
// we'll set the default fallback access token here.
$fb->setDefaultAccessToken('user-access-token');

$requestUserName = $fb->request('GET', '/143498529659602/feed');
$json_string = json_encode($requestUserName, JSON_PRETTY_PRINT);
echo $json_string;