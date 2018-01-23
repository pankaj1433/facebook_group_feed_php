
<head>
<script type="text/javascript" src="//connect.facebook.net/en_US/sdk.js"></script>
</head>
<body>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '992320760924250',
      cookie     : true,
      xfbml      : true,
      version    : 'v2.11'
    });
      
    FB.AppEvents.logPageView();   
      
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "https://connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));

   console.log('hiii')
   var a = FB.getLoginStatus(function(response) {
        statusChangeCallback(response);
    });
    console.log(a)

</script>
</body>
