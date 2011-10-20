<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"
      xmlns:og="http://opengraphprotocol.org/schema/"
      xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <meta http-equiv='content-type' content='text/html;charset=utf-8'/>
    <meta name='description' content="{$meta_description}"/>
    <meta name='keywords' content="{$meta_keywords}"/>
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
    <link href='/rss/blog' rel='alternate' type='application/rss+xml' title='RSS'/>
    <link href='%PATH_PUBLIC%/favicon.ico' rel='shortcut icon' type='image/x-icon'/>
    <link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700&v2' rel='stylesheet' type='text/css'>
    <link href='%PATH_CSS%/core/essential{$_compress_files_suffix_}.css' rel='stylesheet' type='text/css' media='screen, projection'/>
    <link href='%PATH_CSS%/core/application{$_compress_files_suffix_}.css' rel='stylesheet' type='text/css' media='screen, projection'/>
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
  <!--[if lt IE 7]><body class="ie6"><![endif]-->
  <!--[if IE 7]><body class="ie7><![endif]-->
  <!--[if IE 8]><body class="ie8><![endif]-->
  <!--[if gt IE 8]><!--> <body><!--<![endif]-->
    <nav>
      <ul>
        {if $USER_RIGHT > 0}
          <li><strong>{$lang.global.welcome} <a href='/user/{$USER_ID}'>{$USER_NAME}</a>!</strong></li>
        {/if}
        <li><a href='/blog'>{$lang.global.blog}</a></li>
        <li><a href='/gallery'>{$lang.global.gallery}</a></li>
        <li><a href='/download'>{$lang.global.download}</a></li>
        <li><a href='/search'>{$lang.global.search}</a></li>
        {if $USER_RIGHT == 0}
          <li class="right"><a href='/user/create'>{$lang.global.register}</a></li>
          <li class="right"><a href='/session/create'>{$lang.global.login}</a></li>
        {else}
          {if $USER_ID > 0}
            <li><a href='/user/update'>{$lang.global.settings}</a></li>
          {/if}
          <li class="right"><a href='/session/destroy'>{$lang.global.logout}</a></li>
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
        <h3>{$lang.global.overview}</h3>
        <ul>
          <li>
            <a href='/About'>{$lang.global.about} {$WEBSITE_NAME}</a>
          </li>
          <li>
            <a href='/Disclaimer'>{$lang.global.disclaimer}</a>
          </li>
          <li>
            <a href='/sitemap'>{$lang.global.sitemap}</a>
          </li>
        </ul>
      </section>
      <section id="settings">
        <h3>{$lang.global.settings}</h3>
        <ul>
          {if $USER_RIGHT >= 3}
            <li>
              <a href='/newsletter/create' title='{$lang.newsletter.title.create}'>
                {$lang.newsletter.title.create}
              </a>
            </li>
            <li>
              <a href='/media' title='{$lang.global.manager.media}'>
                {$lang.global.manager.media}
              </a>
            </li>
            <li>
              <a href='/content' title='{$lang.global.manager.content}'>
                {$lang.global.manager.content}
              </a>
            </li>
            {if $USER_RIGHT == 4}
              <li>
                <a href='/log' title='{$lang.global.logs}'>
                  {$lang.global.logs}
                </a>
              </li>
              <li>
                <a href='/user' title='{$lang.global.manager.user}'>
                  {$lang.global.manager.user}
                </a>
              </li>
            {/if}
          {else}
            <li>
              <a href='/newsletter' title='{$lang.newsletter.title.handle}'>
                {$lang.newsletter.title.handle}
              </a>
            </li>
          {/if}
        </ul>
      </section>
    </footer>
    <script type='text/javascript'>{$_javascript_language_file_}</script>
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