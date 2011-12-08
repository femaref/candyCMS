{if $_facebook_plugin_ == true}
  <div id="fb-root"></div>
  <script type="text/javascript">
    var sFacebookAppId = '{$FACEBOOK_APP_ID}';
    {literal}
      window.fbAsyncInit = function() {
              FB.init({appId: sFacebookAppId, status: true, cookie: true,
                xfbml: true});
            };
    {/literal}
    (function() {
      var e = document.createElement('script'); e.async = true;
      e.src = document.location.protocol +
        '//connect.facebook.net/{$WEBSITE_LOCALE}/all.js';
      document.getElementById('fb-root').appendChild(e);
    }());
  </script>
{/if}