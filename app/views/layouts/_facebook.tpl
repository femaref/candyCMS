{if $_facebook_plugin_ == true}
  <div id="fb-root"></div>
  <script type="text/javascript">
    var sFacebookAppId = '{$FACEBOOK_APP_ID}';
      window.fbAsyncInit = function() {
        FB.init({ appId: sFacebookAppId, status: true, cookie: true,
          xfbml: true, oauth: true });
      };
    (function(d){
      var js, id = 'facebook-jssdk'; if (d.getElementById(id)) { return; }
      js = d.createElement('script'); js.id = id; js.async = true;
      js.src = "//connect.facebook.net/{$WEBSITE_LOCALE}/all.js";
      d.getElementsByTagName('head')[0].appendChild(js);
    }(document));
  </script>
{/if}