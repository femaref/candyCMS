<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"
      xmlns:og="http://opengraphprotocol.org/schema/"
      xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <meta http-equiv='content-type' content='text/html;charset=utf-8'/>
    <meta name='description' content="{$meta_description}"/>
    <meta name='keywords' content="{$meta_keywords}"/>
    <meta name="google-site-verification" content="lQ0NQ4LxSrq_3ouY1zEma88E0Zf8h91-bh_25tDStrE" />
    {if $_request_id_}
      <meta property="og:description" content="{$meta_og_description}"/>
      <meta property="og:site_name" content="{$meta_og_site_name}"/>
      <meta property="og:title" content="{$meta_og_title}"/>
      <meta property="og:url" content="{$meta_og_url}"/>
    {/if}
    {if $_facebook_plugin_ == true}
      <meta property="fb:admins" content="{$FACEBOOK_ADMIN_ID}"/>
      <meta property="fb:app_id" content="{$FACEBOOK_APP_ID}"/>
    {/if}
    <link href='{$WEBSITE_URL}/rss/blog' rel='alternate' type='application/rss+xml' title='RSS'/>
    <link href='%PATH_TEMPLATE%/favicon.ico' rel='shortcut icon' type='image/x-icon'/>
    <link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700&v2' rel='stylesheet' type='text/css'>
    <link href='%PATH_CSS%/core/essential{$_compress_files_suffix_}.css' rel='stylesheet' type='text/css' media='screen, projection'/>
    <link href='%PATH_CSS%/core/application{$_compress_files_suffix_}.css' rel='stylesheet' type='text/css' media='screen, projection'/>
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1{$_compress_files_suffix_}.js"></script>
    <script type="text/javascript">
      {* Fallback to local jQuery when no Google access *}
      if (typeof jQuery == 'undefined')
        document.write(unescape("%3Cscript src='%PATH_JS%/core/jquery.1.7.1{$_compress_files_suffix_}.js' type='text/javascript'%3E%3C/script%3E"));
      {* Load JSON language variables  *}
      var lang = {$_json_language_};
    </script>
    <title>{$_title_}</title>
  </head>
  <!--[if lt IE 7]><body class="ie6"><![endif]-->
  <!--[if IE 7]><body class="ie7><![endif]-->
  <!--[if IE 8]><body class="ie8><![endif]-->
  <!--[if gt IE 8]><!--> <body><!--<![endif]-->
    <aside>
      <ul>
        <li>
          <a class="js-fancybox" title="Marco Raddatz" href="http://www.gravatar.com/avatar/1a578e32b3c7216f1ecd8e5f31fe0169.jpg?s=800">
            <img width="100" height="100" alt="Marco" src="http://www.gravatar.com/avatar/1a578e32b3c7216f1ecd8e5f31fe0169.jpg?s=100">
          </a>
        </li>
        <li>
          <a href="/">Home</a>
        </li>
        <li>
          <a rel="external" href="http://www.facebook.com/marcoraddatz">Facebook</a>
        </li>
        <li>
          <a rel="external" href="https://www.xing.com/profile/Marco_Raddatz2">XING</a>
        </li>
        <li>
          <a rel="external" href="https://twitter.com/marcoraddatz">Twitter</a>
        </li>
        <li>
          <a rel="external" href="callto://marcoraddatz">Skype</a>
        </li>
        <li>
          <a rel="external" href="/mail/1">Mail</a>
        </li>
        <li>
          <a rel="external" href="/rss/blog">RSS</a>
        </li>
        <li>
          <a href="%PATH_UPLOAD%/media/marcoraddatz.vcf">vCard</a>
        </li>
        <li>
          <a href="/Impressum">{$lang.global.disclaimer}</a>
        </li>
      </ul>
    </aside>
    <div id="content">
      {if $_flash_type_}
        <div id='js-flash_message'>
          <div class='{$_flash_type_}' id='js-flash_{$_flash_type_}'>
            <h4>{$_flash_headline_}</h4>
            <p>{$_flash_message_}</p>
          </div>
        </div>
      {/if}
      {$_content_}
    </div>
    <script type='text/javascript' src='%PATH_JS%/core/scripts{$_compress_files_suffix_}.js'></script>
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
    {if $WEBSITE_TRACKING_CODE}
      <script type="text/javascript">
        var sTrackingCode = '{$WEBSITE_TRACKING_CODE}';
        {literal}
          var _gaq = _gaq || [];
          _gaq.push(['_setAccount', sTrackingCode]);
          _gaq.push (['_gat._anonymizeIp']);
          _gaq.push(['_trackPageview']);

          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();
        {/literal}
      </script>
    {/if}
  </body>
</html>