<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"
      xmlns:og="http://opengraphprotocol.org/schema/"
      xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <meta http-equiv='content-type' content='text/html;charset=utf-8'/>
    <meta name='description' content="{$meta_description}"/>
    <meta name='keywords' content="{$meta_keywords}"/>
    <meta name='dc.title' content="{$_title_}"/>

    {* Provide more details for specific entry. *}
    {if $_request_id_}
      <meta property="og:description" content="{$meta_og_description}"/>
      <meta property="og:site_name" content="{$meta_og_site_name}"/>
      <meta property="og:title" content="{$meta_og_title}"/>
      <meta property="og:url" content="{$meta_og_url}"/>
      <meta itemprop="name" content="{$meta_og_title}">
      <meta itemprop="description" content="{$meta_og_description}">
    {/if}

    {* If we want to use a facebook plugin, provide tracking data. *}
    {if $_facebook_plugin_ == true}
      <meta property="fb:admins" content="{$FACEBOOK_ADMIN_ID}"/>
      <meta property="fb:app_id" content="{$FACEBOOK_APP_ID}"/>
    {/if}

    {* Basic stuff *}
    <link href='/rss/blog' rel='alternate' type='application/rss+xml' title='RSS'/>
    <link href='%PATH_PUBLIC%/favicon.ico' rel='shortcut icon' type='image/x-icon'/>

    {* Include jQuery and its components *}
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1{$_compress_files_suffix_}.js"></script>

    {* Fallback if CDN is not avaiable. Also include language parts. *}
    <script type="text/javascript">
      if (typeof jQuery == 'undefined')
        document.write(unescape("%3Cscript src='%PATH_JS%/core/jquery.1.7.1{$_compress_files_suffix_}.js' type='text/javascript'%3E%3C/script%3E"));
      var lang = {$_json_language_};
    </script>

    {* Use LESS to minimize CSS *}
    <link rel="stylesheet/less" type="text/css" href="%PATH_LESS%/bootstrap.less"/>
    <script src="%PATH_JS%/core/less.js" type="text/javascript"></script>

    {* Add bootstrap support *}
    <script type='text/javascript' src='%PATH_JS%/core/jquery.bootstrap.buttons{$_compress_files_suffix_}.js'></script>
    <script type='text/javascript' src='%PATH_JS%/core/jquery.bootstrap.dropdown{$_compress_files_suffix_}.js'></script>
    <script type='text/javascript' src='%PATH_JS%/core/jquery.bootstrap.modal{$_compress_files_suffix_}.js'></script>
    <script type='text/javascript' src='%PATH_JS%/core/jquery.bootstrap.tabs{$_compress_files_suffix_}.js'></script>
    <script type='text/javascript' src='%PATH_JS%/core/jquery.bootstrap.tooltip{$_compress_files_suffix_}.js'></script>
    <script type='text/javascript' src='%PATH_JS%/core/jquery.tablesorter{$_compress_files_suffix_}.js'></script>
    <title>{$_title_}</title>
  </head>
  <!--[if lt IE 7]><body class="ie6"><![endif]-->
  <!--[if IE 7]><body class="ie7"><![endif]-->
  <!--[if IE 8]><body class="ie8"><![endif]-->
  <!--[if gt IE 8]><!--><body><!--<![endif]-->
    <div class="topbar" data-dropdown="dropdown">
      <div class="fill">
        <nav class="container">
          <a href="/" class="brand" title="{$WEBSITE_NAME}">
            {$WEBSITE_NAME}
          </a>
          <ul class="nav">
            <li{if $smarty.get.section == 'blog'} class='active'{/if}>
              <a href='/blog'>{$lang.global.blog}</a>
            </li>
            <li{if $smarty.get.section == 'gallery'} class='active'{/if}>
              <a href='/gallery'>{$lang.global.gallery}</a>
            </li>
            <li{if $smarty.get.section == 'calendar'} class='active'{/if}>
              <a href='/calendar'>{$lang.global.calendar}</a>
            </li>
            <li{if $smarty.get.section == 'download'} class='active'{/if}>
              <a href='/download'>{$lang.global.download}</a>
            </li>
          </ul>
          <form method='post' action="/search">
            <input type="text" name="search" placeholder="{$lang.global.search}"/>
          </form>
          <ul class="nav secondary-nav">
            <li class="menu">
              <a href="#" class="menu">
                {if $USER_ID == 0}
                  {$lang.global.login}
                {elseif $USER_ID > 0}
                  <strong>{$lang.global.welcome} {$USER_NAME}!</strong>
                {/if}
              </a>
              <ul class="dropdown-menu">
                {if $USER_ID == 0}
                  <li><a href='/user/create'>{$lang.global.register}</a></li>
                  <li><a href='/session/create'>{$lang.global.login}</a></li>
                {elseif $USER_ID > 0}
                  <li><a href='/user/update'>{$lang.global.settings}</a></li>
                  <li><a href='/session/destroy'>{$lang.global.logout}</a></li>
                {/if}
                {if $USER_ROLE >= 3}
                  <li class="divider"></li>
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
                  {if $USER_ROLE == 4}
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
                {/if}
              </ul>
            </li>
          </ul>
        </nav>
      </div>
    </div>
    <div class="container">
      {if $_flash_type_}
        <div id='js-flash_message'>
          <div class='alert-message {$_flash_type_}' id='js-flash_{$_flash_type_}'>
            <a class="close" href="#">×</a>
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
      <section id="{$smarty.get.section}">
        {$_content_}
      </section>
      <footer id="footer" class="row">
        <section id="about" class="span8">
          <ul>
            <li>
              <a href='/Disclaimer'>{$lang.global.disclaimer}</a>
            </li>
            <li>
              <a href='/sitemap'>{$lang.global.sitemap}</a>
            </li>
          </ul>
        </section>
        <section id="settings" class="span8">
          <ul>
            {if $USER_ROLE < 1}
              <li>
                <a href='/newsletter' title='{$lang.newsletter.title.subscribe}'>
                  {$lang.newsletter.title.subscribe}
                </a>
              </li>
            {/if}
            {if $MOBILE_DEVICE == true}
              <a href='/?mobile=1' ref='nofollow'>{$lang.global.view.mobile}</a>
            {/if}
          </ul>
        </section>
      </footer>
    </div>
    <script type='text/javascript' src='%PATH_JS%/core/scripts{$_compress_files_suffix_}.js'></script>
    {include file="_facebook.tpl"}
    {include file="_google.tpl"}
  </body>
</html>