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
echo "this is test APPlication"."/n";
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

if(isset($accessToken)){
    if(isset($_SESSION['facebook_access_token'])){
        $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
    }else{
        // Put short-lived access token in session
        $_SESSION['facebook_access_token'] = (string) $accessToken;
        
          // OAuth 2.0 client handler helps to manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();
        
        // Exchanges a short-lived access token for a long-lived one
        $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
        $_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
        
        // Set default access token to be used in script
        $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
    }
    
    // Redirect the user back to the same page if url has "code" parameter in query string
    if(isset($_GET['code'])){
        header('Location: ./');
    }
    
    // Getting user facebook profile info
    // try {
    //     $groupFeedResponse = $fb->request('GET', '/143498529659602/feed');
    // } catch(FacebookResponseException $e) {
    //     echo 'Graph returned an error: ' . $e->getMessage();
    //     session_destroy();
    //     // Redirect user back to app login page
    //     header("Location: ./");
    //     exit;
    // } catch(FacebookSDKException $e) {
    //     echo 'Facebook SDK returned an error: ' . $e->getMessage();
    //     exit;
    // }
    
    // Initialize User class
    // $user = new User();
    
    // // Insert or update user data to the database
    // $fbUserData = array(
    //     'oauth_provider'=> 'facebook',
    //     'oauth_uid'     => $fbUserProfile['id'],
    //     'first_name'    => $fbUserProfile['first_name'],
    //     'last_name'     => $fbUserProfile['last_name'],
    //     'email'         => $fbUserProfile['email'],
    //     'gender'        => $fbUserProfile['gender'],
    //     'locale'        => $fbUserProfile['locale'],
    //     'picture'       => $fbUserProfile['picture']['url'],
    //     'link'          => $fbUserProfile['link']
    // );
    // $userData = $user->checkUser($fbUserData);
    
    // Put user data into session
    // $_SESSION['userData'] = $userData;
    $_SESSION['feeds'] = $fb->request('GET', '/143498529659602/feed');

    // Get logout url
    // $logoutURL = $helper->getLogoutUrl($accessToken, $redirectURL.'logout.php');
    
    // Render facebook profile data
    // if(!empty($userData)){
    //     $output  = '<h1>Facebook Profile Details </h1>';
    //     $output .= '<img src="'.$userData['picture'].'">';
    //     $output .= '<br/>Facebook ID : ' . $userData['oauth_uid'];
    //     $output .= '<br/>Name : ' . $userData['first_name'].' '.$userData['last_name'];
    //     $output .= '<br/>Email : ' . $userData['email'];
    //     $output .= '<br/>Gender : ' . $userData['gender'];
    //     $output .= '<br/>Locale : ' . $userData['locale'];
    //     $output .= '<br/>Logged in with : Facebook';
    //     $output .= '<br/><a href="'.$userData['link'].'" target="_blank">Click to Visit Facebook Page</a>';
    //     $output .= '<br/>Logout from <a href="'.$logoutURL.'">Facebook</a>'; 
    // }else{
    //     $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
    // }
    
}else{
    // Get login url
    $loginURL = $helper->getLoginUrl($redirectURL, $fbPermissions);
    echo htmlspecialchars($loginURL);
    // Render facebook login button
    $output = '<a href="'.htmlspecialchars($loginURL).'">login</a>';
}
?>
<html>
<head>
<title>Login with Facebook using PHP</title>
<style type="text/css">
    h1{font-family:Arial, Helvetica, sans-serif;color:#999999;}
</style>
</head>
<body>
    <!-- Display login button / Facebook profile information -->
    <div><?php var_dump($_SESSION['feeds']); ?></div>
    <div>
            <?php
                // echo '<pre>';
                // var_dump($_SESSION);
                // echo '</pre>';
            ?>
    </div>
    <br><br><br><br><br>
    <a href='group.php' >go to group feeds</group>
</body>
</html>