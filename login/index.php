<?php
// Include FB config file.
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
        $profileRequest = $fb->get('/me?fields=first_name,last_name');
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

    // Render facebook profile data
    if(!empty($fbUserProfile)){
        //store in sql here
        $servername = "localhost";$username = "root";$password = "";$dbname = "facebook";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        else{
                $sql = "INSERT INTO user_token (user_name, access_token, time)VALUES ('".$fbUserProfile['first_name']." ".$fbUserProfile['last_name']."', '".$_SESSION['facebook_access_token']."', now())";
                if ($conn->query($sql) === TRUE) {
                    $output = "<h3>Token Updated Successfully</h3>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }else{
        $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
    }
}else{
    $loginURL = $helper->getLoginUrl($redirectURL, $fbPermissions);
    $output = '<a href="'.htmlspecialchars($loginURL).'">Update Access Token</a>';
}
?>
<div><?php echo $output; ?></div>