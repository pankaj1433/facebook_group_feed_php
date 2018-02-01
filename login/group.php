<?php

require_once 'fbConfig.php';
$servername = "localhost";$username = "root";$password = "";$dbname = "facebook";
$conn = new mysqli($servername, $username, $password, $dbname);
$access_token = '';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
else{
        $sql = "SELECT * from  user_token LIMIT 1";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $access_token = $row['access_token'];
        }
        else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
}
if(!empty($access_token)) {
    try {
        $response = $fb->get('/143498529659602/feed?fields=story,created_time,from{name, picture, link},id,permalink_url,message,comments,likes{total_count},picture,attachments,sharedposts',$access_token);
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
      } catch(Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
      }
}
$feeds = $response->getDecodedBody()['data'];
// print_r($feeds);
?>
<html>
    <head>
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
    </head>
<body>
    <div class = "container">
        <div class="top-bar">
            <img src='assets/facebook-logo.png' height="30",width="30">
        </div>
        <div class = "content">
            <?php
                foreach($feeds as $feed) {
                    if(!empty($feed['message'])){
            ?>
                    <div class = "feed-card">
                        <div>
                            <img class="profile-thumb" src="<?php echo $feed['from']['picture']['data']['url']?>">
                            <div>
                                <a class="profile-name" href="<? echo $feed['from']['link']?>" ><? echo $feed['from']['name'] ?></a>
                                <span class = "created-time"><? echo date('F j ',strtotime($feed['created_time'])).'at'.date(' g:ia',strtotime($feed['created_time'])) ?></span>
                            </div>
                        </div>
                        <div class="feed-msg-wrapper">
                            <p class="feed-msg"><? echo $feed['message'] ?></p>
                        </div>
                        <div class="like-cmnt-wrapper">
                            <i class="fb-icon far fa-thumbs-up"></i>
                            <span class="icon-count"><? if(!empty($feed['likes'])) echo count($feed['likes']['data']); else echo "0";?></span>
                            <i class="fb-icon far fa-comment"></i>
                            <span class="icon-count"><? if(!empty($feed['comments'])) echo count($feed['comments']['data']); else echo "0"; ?></span>
                        </div>
                    </div>
                    
                        
            <?php
                    }
                    else if(!empty($feed['story'])){
            ?>
                    <div class = "feed-card">
                        <div>
                            <img class="profile-thumb" src="<?php echo $feed['from']['picture']['data']['url']?>">
                            <div>
                                <a class="profile-name" href="<? echo $feed['from']['link']?>" ><? echo $feed['from']['name'] ?></a>
                                <span class = "created-time"><? echo date('F j ',strtotime($feed['created_time'])).'at'.date(' g:ia',strtotime($feed['created_time'])) ?></span>
                            </div>
                        </div>
                        <div class="feed-msg-wrapper">
                            <p class="feed-story"><? echo $feed['story'] ?></p>
                        </div>
                        <div class="like-cmnt-wrapper">
                            <i class="fb-icon far fa-thumbs-up"></i>
                            <span class="icon-count"><? if(!empty($feed['likes'])) echo count($feed['likes']['data']); else echo "0";?></span>
                            <i class="fb-icon far fa-comment"></i>
                            <span class="icon-count"><? if(!empty($feed['comments'])) echo count($feed['comments']['data']); else echo "0"; ?></span>
                        </div>
                    </div>
            <?php
                    }
                }
            ?>
        </div>
    </div>
    
</body>

<style>
    .container {
        width:40%;
    }
    .top-bar {
        background-color: #3b579c;
        width:100%;
        padding:5px;
    }
    .content {
        background-color: #f6f7f9;
        width:100%;
        padding:5px;
    }
    .feed-card {
        margin: 5px;
        padding:12px 12px 0;
        border-radius: 3%;
        background-color: #ffffff;
        width: calc ( 100% - 10px) ;
        overflow: hidden;   
    }
    .feed-card>div {
        width: calc ( 100% - 10px) ;
        clear: both
    }
    .profile-thumb {
        border-radius:50%;
        height:40px;
        width:40px;
        overflow:hidden;
        margin-right: 8px;
        float: left;
        display: block;
    }
    .profile-name {
        font-family: sans-serif;
        text-decoration: none;
        color: #365899;
        cursor: pointer;
        font-weight: bold;
        font-size: 14px;
        line-height: 1.38;
        margin-bottom: 2px;
    }
    .created-time {
        font-family: sans-serif;
        display: block;
        color: #90949c;
        font-size: 12px;
        font-weight: normal;    
    }
    .feed-msg {
        font-family: sans-serif;
        font-size: 24px;
        font-weight: 300;
        letter-spacing: 0;
        line-height: 28px;
        margin: 14px 0 7px 0;
    }
    .feed-story {
        font-family: sans-serif;
        color: #90949c;
        font-weight: normal;
        font-size: 14px;
        line-height: 1.38;
    }
    .feed-msg-wrapper {
       border-bottom: 1px solid #f0f0f0;
    }
    .like-cmnt-wrapper {
        padding: 12px 12px;
    }
    .fb-icon {
        color: #90949c;
        margin-right: 4px;
        font-size: 12px;
    }
    .icon-count {
        font-size: 12px;
        font-weight: bold;
        line-height: 16px;
        font-family: sans-serif;
        color: #90949c;
        margin-right: 16px;
    }
</style>

</html>