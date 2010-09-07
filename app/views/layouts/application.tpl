<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv='content-type' content='text/html;charset=utf-8' />
    <meta name='description' content='{$meta_description}' />
    <link href='{$_website_url_}/RSS/blog' rel='alternate' type='application/rss+xml' title='RSS' />
    <link href='%PATH_PUBLIC%/favicon.ico' rel='shortcut icon' type='image/x-icon' />
    <link href='%PATH_CSS%/essential{$_compress_files_suffix_}.css' rel='stylesheet' type='text/css' media='screen, projection' />
    <link href='%PATH_CSS%/style{$_compress_files_suffix_}.css' rel='stylesheet' type='text/css' media='screen, projection' />
    <script language='javascript' src='%PATH_PUBLIC%/js/core/mootools{$_compress_files_suffix_}.js' type='text/javascript'></script>
    <title>{$_title_}</title>
  </head>
  <body>
    <div id='container'>
      <div id="header">
        <div id='navigation'>
          <ul>
            {if $USER_ID > 0}
              <li><strong>{$lang_welcome} <a href='/User/{$USER_ID}'>{$user}</a>!</strong></li>
            {/if}
            <li><a href='/Blog'>{$lang_blog}</a></li>
            <li><a href='/Gallery'>{$lang_gallery}</a></li>
            {if $USER_ID == 0}
              <li><a href='/Session/create'>{$lang_login}</a></li>
              <li><a href='/User/create'>{$lang_register}</a></li>
            {else}
              <li><a href='/User/update'>{$lang_settings}</a></li>
              <li><a href='/Session/destroy'>{$lang_logout}</a></li>
            {/if}
          </ul>
        </div>
      </div>
      <div id='body'>
        {if $lang_update_avaiable}
          <div class="notice">
            {$lang_update_avaiable}
          </div>
        {/if}
        <div id='js-flash_message'>
          <div class='%FLASH_TYPE%' id='js-flash_%FLASH_TYPE%'
               onclick="hideDiv('js-flash_message')">
            <h4>%FLASH_HEADLINE%</h4>
            <p>%FLASH_MESSAGE%</p>
          </div>
        </div>
        {$_plugin_archive_}
        {$_content_}
      </div>
      <div id="footer">
        <p>
          <a href='/About'>{$lang_about} {$name}</a> &middot; <a href='/Disclaimer'>{$lang_disclaimer}</a> &middot; <a href='/Contact/Bugreport'>{$lang_report_error}</a>
        </p>
        <ul>
          {if $USER_RIGHT > 3}
            <li>
              <a href='/Newsletter/create' title='{$lang_newsletter_send}'>
                <img src='%PATH_IMAGES%/spacer.gif' class="icon-email" alt='' />
                {$lang_newsletter_send}</a>
            </li>
            <li>
              <a href='/Media' title='{$lang_filemanager}'>
                <img src='%PATH_IMAGES%/spacer.gif' class="icon-folder" alt='' />
                {$lang_filemanager}</a>
            </li>
            <li>
              <a href='/Content' title='{$lang_contentmanager}'>
                <img src='%PATH_IMAGES%/spacer.gif' class="icon-manager" alt='' />
                {$lang_contentmanager}</a>
            </li>
            <li>
              <a href='/User' title='{$lang_usermanager}'>
                <img src='%PATH_IMAGES%/spacer.gif' class="icon-user" alt='' />
                {$lang_usermanager}</a>
            </li>
          {else}
            <li>
              <a href='/Newsletter' title='{$lang_newsletter_handle}'>
                <img src='%PATH_IMAGES%/spacer.gif' class="icon-email" alt='' />
                {$lang_newsletter_handle}</a>
            </li>
          {/if}
        </ul>
      </div>
    </div>
    <script language='javascript' type='text/javascript'>{$_javascript_language_file_}</script>
    <script language='javascript' src='%PATH_PUBLIC%/js/core/javascript{$_compress_files_suffix_}.js' type='text/javascript'></script>
  </body>
</html>