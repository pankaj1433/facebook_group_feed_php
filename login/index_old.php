<?php
// Include FB config file && User class
require_once 'fbConfig.php';

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
    try {
        $profileRequest = $fb->get('/me?fields=name,first_name,last_name,email,link,gender,locale,picture');
        $fbUserProfile = $profileRequest->getGraphNode()->asArray();
    } catch(FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        session_destroy();
        // Redirect user back to app login page
        header("Location: ./");
        exit;
    } catch(FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
    
    // Initialize User class
    // $user = new User();
    
    // Insert or update user data to the database
    $fbUserData = array(
        'oauth_provider'=> 'facebook',
        'oauth_uid'     => $fbUserProfile['id'],
        'first_name'    => $fbUserProfile['first_name'],
        'last_name'     => $fbUserProfile['last_name'],
        'email'         => $fbUserProfile['email'],
        'gender'        => $fbUserProfile['gender'],
        'locale'        => $fbUserProfile['locale'],
        'picture'       => $fbUserProfile['picture']['url'],
        'link'          => $fbUserProfile['link']
    );
    $userData = $fbUserData;
    
    // Put user data into session
    $_SESSION['userData'] = $fbUserData;
    
    // Get logout url
    // $logoutURL = $helper->getLogoutUrl($accessToken, $redirectURL.'logout.php');
    
    // Render facebook profile data
    if(!empty($userData)){
        $output  = '<h1>Facebook Profile Details </h1>';
        $output .= '<img src="'.$userData['picture'].'">';
        $output .= '<br/>Facebook ID : ' . $userData['oauth_uid'];
        $output .= '<br/>Name : ' . $userData['first_name'].' '.$userData['last_name'];
        $output .= '<br/>Email : ' . $userData['email'];
        $output .= '<br/>Gender : ' . $userData['gender'];
        $output .= '<br/>Locale : ' . $userData['locale'];
        $output .= '<br/>Logged in with : Facebook';
        $output .= '<br/><a href="'.$userData['link'].'" target="_blank">Click to Visit Facebook Page</a>';
        // $output .= '<br/>Logout from <a href="'.$logoutURL.'">Facebook</a>'; 
    }else{
        $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
    }
    
    try {
        // $group_response_one = $fb->request('GET', '/143498529659602/feed');
        // $group_response = json_decode($group_response_one, true);
        $response = $fb->get('/143498529659602/feed?fields=created_time,from,id,permalink_url,message,comments,likes,picture,attachments,reactions,sharedposts',$_SESSION['facebook_access_token']);
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
      } catch(Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
      }
}else{
    // Get login url
    echo "REDIRect url :    ".$redirectURL;
    $loginURL = $helper->getLoginUrl($redirectURL, $fbPermissions);
    echo "<br>"."login url:  ".$loginURL."<br>";
    // Render facebook login button
    $output = '<a href="'.htmlspecialchars($loginURL).'">login</a>';
}

?>
<html>
<head>
<title>Login with Facebook using PHP by CodexWorld</title>
<style type="text/css">
    h1{font-family:Arial, Helvetica, sans-serif;color:#999999;}
</style>
</head>
<body>
    <?php
    // $servername = "localhost";
    // $username = "root";
    // $password = "";
    // $dbname = "facebook";
    // $conn = new mysqli($servername, $username, $password, $dbname);
    // if ($conn->connect_error) {
    //     die("Connection failed: " . $conn->connect_error);
    // } 
    // else{
    //         $sql = "INSERT INTO user_token (user_name, access_token, time)VALUES ('John', '".$longLivedAccessToken."', now())";
    //         echo $sql;
    //         if ($conn->query($sql) === TRUE) {
    //         echo "New record created successfully";
    //     } else {
    //         echo "Error: " . $sql . "<br>" . $conn->error;
    //     }
        
    // }
    
?>
    <!-- Display login button / Facebook profile information -->
    <div><?php echo $output; ?></div>
    <?php if(isset($_SESSION['facebook_access_token'])) { ?>
    <div><?php var_dump($_SESSION['facebook_access_token']); ?></div>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "facebook";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    else{
            $sql = "INSERT INTO user_token (user_name, access_token, time)VALUES ('John', '".$_SESSION['facebook_access_token']."', now())";
            echo $sql;
            if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        
    }
    
?>
    <br>
    <h3>GRoup response</h3>
    <div><?php var_dump($response->getBody()) ?></div>
    <h3>array resp</h3>
    <div>
    <pre><?php print_r($response->getDecodedBody()[data]); ?></pre>
    </div>
    <div>
    <?php } ?>
    <?php
    // try {
        // Returns a `Facebook\FacebookResponse` object
    //     $post_response = $fb->get($response->getDecodedBody()[data][12][id],$_SESSION['facebook_access_token']);
    //     } catch(Facebook\Exceptions\FacebookResponseException $e) {
    //         echo 'Graph returned an error: ' . $e->getMessage();
    //         exit;
    //     } catch(Facebook\Exceptions\FacebookSDKException $e) {
    //         echo 'Facebook SDK returned an error: ' . $e->getMessage();
    //         exit;
    //     }
    //     echo "<h2>post response</h2>";
    //    var_dump($post_response->getGraphEdge());
    ?>
    </div>
</body>
</html>