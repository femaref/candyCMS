<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"
      xmlns:og="http://opengraphprotocol.org/schema/"
      xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <meta http-equiv='content-type' content='text/html;charset=utf-8' />
    <meta name='description' content="{$meta_description}" />
    <meta name='keywords' content="{$meta_keywords}" />
    {if $_id_}
      <meta property="og:title" content="{$meta_og_title}"/>
      <meta property="og:url" content="{$meta_og_url}"/>
      <meta property="og:site_name" content="{$meta_og_site_name}"/>
    {/if}
    {if $_facebook_app_id_}
      <meta property="fb:app_id" content="{$FACEBOOK_APP_ID}"/>
    {/if}
    <link href='{$WEBSITE_URL}/rss/blog' rel='alternate' type='application/rss+xml' title='RSS' />
    <link href='%PATH_PUBLIC%/favicon.ico' rel='shortcut icon' type='image/x-icon' />
    <link href='%PATH_CSS%/essential{$_compress_files_suffix_}.css' rel='stylesheet' type='text/css' media='screen, projection' />
    <link href='%PATH_CSS%/style{$_compress_files_suffix_}.css' rel='stylesheet' type='text/css' media='screen, projection' />
    <script language='javascript' src='%PATH_PUBLIC%/js/core/jquery.1.5.2{$_compress_files_suffix_}.js' type='text/javascript'></script>
    <title>{$_title_}</title>
    <!--[if IE]>
      <link href='%PATH_CSS%/ie{$_compress_files_suffix_}.css' rel='stylesheet' type='text/css' media='screen, projection' />
    <![endif]-->
  </head>
  <body>
    <nav>
      <ul>
        {if $USER_ID > 0}
          <li><strong>{$lang_welcome} <a href='/user/{$USER_ID}'>{$user}</a>!</strong></li>
        {/if}
        <li><a href='/blog'>{$lang_blog}</a></li>
        <li><a href='/gallery'>{$lang_gallery}</a></li>
        <li><a href='/search'>{$lang_search}</a></li>
        {if $USER_ID == 0}
          <li><a href='/session/create'>{$lang_login}</a></li>
          <li><a href='/user/create'>{$lang_register}</a></li>
        {else}
          <li><a href='/user/update'>{$lang_settings}</a></li>
          <li><a href='/session/destroy'>{$lang_logout}</a></li>
        {/if}
      </ul>
    </nav>
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
    <footer id="footer">
      <section id="about">
        <header>
          <h3>About</h3>
        </header>
        <ul>
          <li>
            <a href='/About'>{$lang_about} {$WEBSITE_NAME}</a>
          </li>
          <li>
            <a href='/Disclaimer'>{$lang_disclaimer}</a>
          </li>
        </ul>
      </section>
      <section id="settings">
        <header>
          <h3>Settings</h3>
        </header>
        <ul>
          {if $USER_RIGHT >= 3}
            <li>
              <a href='/newsletter/create' title='{$lang_newsletter_create}'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-email" alt='' />
                {$lang_newsletter_create}</a>
            </li>
            <li>
              <a href='/media' title='{$lang_filemanager}'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-folder" alt='' />
                {$lang_filemanager}</a>
            </li>
            <li>
              <a href='/content' title='{$lang_contentmanager}'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-manager" alt='' />
                {$lang_contentmanager}</a>
            </li>
            {if $USER_RIGHT == 4}
              <li>
                <a href='/log' title='{$lang_logs}'>
                  <img src='%PATH_IMAGES%/spacer.png' class="icon-manager" alt='' />
                  {$lang_logs}</a>
              </li>
              <li>
                <a href='/user' title='{$lang_usermanager}'>
                  <img src='%PATH_IMAGES%/spacer.png' class="icon-user" alt='' />
                  {$lang_usermanager}</a>
              </li>
            {/if}
          {else}
            <li>
              <a href='/newsletter' title='{$lang_newsletter_handle}'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-email" alt='' />
                {$lang_newsletter_handle}</a>
            </li>
          {/if}
        </ul>
      </section>
    </footer>
    <script language='javascript' type='text/javascript'>{$_javascript_language_file_}</script>
    <script language='javascript' src='%PATH_PUBLIC%/js/core/scripts{$_compress_files_suffix_}.js' type='text/javascript'></script>
    {if $FACEBOOK_APP_ID}
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