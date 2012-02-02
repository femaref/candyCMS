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
    {if $FACEBOOK_ADMIN_ID}
      <meta property="fb:admins" content="{$FACEBOOK_ADMIN_ID}"/>
    {/if}
    {if $FACEBOOK_APP_ID}
      <meta property="fb:app_id" content="{$FACEBOOK_APP_ID}"/>
    {/if}
    <link href='{$WEBSITE_URL}/rss/blog' rel='alternate' type='application/rss+xml' title='RSS'/>
    <link href='%PATH_TEMPLATE%/favicon.ico' rel='shortcut icon' type='image/x-icon'/>
    <link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700&v2' rel='stylesheet' type='text/css'>
    <link href='%PATH_CSS%/essential{$_compress_files_suffix_}.css' rel='stylesheet' type='text/css' media='screen, projection'/>
    <link href='%PATH_CSS%/style{$_compress_files_suffix_}.css' rel='stylesheet' type='text/css' media='screen, projection'/>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery{$_compress_files_suffix_}.js"></script>
    <script type="text/javascript">
      if (typeof jQuery == 'undefined')
        document.write(unescape("%3Cscript src='%PATH_PUBLIC%/js/core/jquery.1.6.2{$_compress_files_suffix_}.js' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <title>{$_title_}</title>
    <!--[if IE]>
      <link href='%PATH_CSS%/ie{$_compress_files_suffix_}.css' rel='stylesheet' type='text/css' media='screen, projection'/>
      <script src='%PATH_PUBLIC%/js/core/ie.html5{$_compress_files_suffix_}.js' type='text/javascript'></script>
    <![endif]-->
  </head>
  <body>
    <header id="header">
      <a href="/">MarcoRaddatz.com</a>
      <h2>{$meta_description}</h2>
    </header>
    <aside>
      <div id="aside-contact">
        <div id="image">
          <a class="js-fancybox" title="Marco Raddatz" href="http://www.gravatar.com/avatar/1a578e32b3c7216f1ecd8e5f31fe0169.jpg?s=800">
            <img width="100" height="100" alt="Marco" src="http://www.gravatar.com/avatar/1a578e32b3c7216f1ecd8e5f31fe0169.jpg?s=100">
          </a>
        </div>
        <ul>
          <li>
            <a rel="external" href="http://www.facebook.com/marcoraddatz">
              <img src="%PATH_IMAGES%/spacer.png" alt="Facebook" title="Mein Facebook-Profil"
                   class="contact-facebook js-tooltip" width="32" height="32" />
            </a>
          </li>
          <li>
            <a rel="external" href="https://www.xing.com/profile/Marco_Raddatz2">
              <img src="%PATH_IMAGES%/spacer.png" alt="XING" title="Mein XING-Profil"
                   class="contact-xing js-tooltip" width="32" height="32" />
            </a>
          </li>
          <li>
            <a rel="external" href="https://twitter.com/marcoraddatz">
              <img src="%PATH_IMAGES%/spacer.png" alt="Twitter" title="Mein Twitter-Profil"
                   class="contact-twitter js-tooltip" width="32" height="32" />
            </a>
          </li>
          <li>
            <a rel="external" href="callto://marcoraddatz">
              <img src="%PATH_IMAGES%/spacer.png" alt="Facebook" title="Kommuniziere mit mir über Skype"
                   class="contact-skype js-tooltip" width="32" height="32" />
            </a>
          </li>
          <li>
            <a rel="external" href="/mail/1">
              <img src="%PATH_IMAGES%/spacer.png" alt="Facebook" title="Sende mir eine E-Mail"
                   class="contact-email js-tooltip" width="32" height="32" />
            </a>
          </li>
          <li>
            <a rel="external" href="/rss/blog">
              <img src="%PATH_IMAGES%/spacer.png" alt="Facebook" title="Verfolge meinen Blog"
                   class="contact-rss js-tooltip" width="32" height="32" />
            </a>
          </li>
        </ul>
        <a href="%PATH_UPLOAD%/media/marcoraddatz.vcf">Kontaktdaten als vCard</a> | <a href="/Impressum">{$lang_disclaimer}</a>
      </div>
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
      {if $_update_avaiable_}
        <div class="notice">
          {$_update_avaiable_}
        </div>
      {/if}
      {$_content_}
    </div>
    <script src='%PATH_PUBLIC%/js/plugins/jquery.tipTip{$_compress_files_suffix_}.js' type='text/javascript'></script>
    <script type='text/javascript'>
      {$_javascript_language_file_}
      $(window).scroll(function(){
        $("aside")
        .stop()
        .animate({ "marginTop": ($(window).scrollTop()) + "px" }, "slow" );
      });
      $(".js-tooltip").tipTip({ maxWidth: "auto" });
      $('html').attr('style', 'min-height:' + $(window).height() + 'px');
    </script>
    <script src='%PATH_PUBLIC%/js/core/scripts{$_compress_files_suffix_}.js' type='text/javascript'></script>
    {if $FACEBOOK_APP_ID && $_facebook_plugin_ == true}
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
            '//connect.facebook.net/{$_locale_}/all.js';
          document.getElementById('fb-root').appendChild(e);
        }());
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