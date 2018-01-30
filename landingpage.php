<?php
session_start();
if(isset($_SESSION['facebook_access_token'])){
    echo $_SESSION['facebook_access_token'];
}
else {
    echo "nothing in sessionsss";
}
?>