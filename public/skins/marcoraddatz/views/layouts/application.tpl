<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv='content-type' content='text/html;charset=utf-8' />
    <meta name='description' content="{$meta_description}" />
    <meta name='keywords' content="{$meta_keywords}" />
    <link href='{$_website_url_}/RSS/blog' rel='alternate' type='application/rss+xml' title='RSS' />
    <link href='%PATH_PUBLIC%/favicon.ico' rel='shortcut icon' type='image/x-icon' />
    <link href='%PATH_CSS%/essential{$_compress_files_suffix_}.css' rel='stylesheet' type='text/css' media='screen, projection' />
    <link href='%PATH_CSS%/style{$_compress_files_suffix_}.css' rel='stylesheet' type='text/css' media='screen, projection' />
    <script language='javascript' src='%PATH_PUBLIC%/js/core/mootools{$_compress_files_suffix_}.js' type='text/javascript'></script>
    <title>{$_title_}</title>
  </head>
  <body>
    <div id='navigation'>
       <ul>
        <li><a href='/Blog'>{$lang_blog}</a></li>
        <li><a href='/Gallery'>{$lang_gallery}</a></li>
        <li><a href='/Disclaimer'>{$lang_disclaimer}</a></li>
        {if $USER_ID == 0}
          <li style="float:right"><a href='/User/create'>{$lang_register}</a></li>
          <li style="float:right"><a href='/Session/create'>{$lang_login}</a></li>
        {else}
          <li style="float:right"><a href='/Session/destroy'>{$lang_logout}</a></li>
          <li style="float:right"><a href='/User/update'>{$lang_settings}</a></li>
        {/if}
      </ul>
    </div>
    <div id="contact">
      <p>
        <a href="/upload/user/popup/1.jpg" rel="lightbox">
          <img src="/upload/user/100/1.jpg" width="100" class="image" alt="" />
        </a>
      </p>
      <div id="about">
        Dies ist der Blog von Marco Raddatz, Softwareentwickler aus dem schönen Kiel.
      </div>
      <p>
        <a href='http://www.facebook.com/marcoraddatz' rel='external'>Facebook</a>
        <a href='http://github.com/marcoraddatz' rel='external'>GitHub</a>
        <a href='http://www.studivz.net/Profile/Z8-9k7vbFJJbnaEhntaQVHnGTppx4Mj1fVE6GfIbLXY' rel='external'>studiVZ</a>
        <a href='https://twitter.com/marcoraddatz' rel='external'>Twitter</a>
        <a href='https://www.xing.com/profile/Marco_Raddatz2' rel='external'>XING</a>
        <a href='/Mail/1'>E-Mail</a>
        <a href='/upload/media/marcoraddatz.vcf'>vCard</a>
      </p>
    </div>
    <div id='body'>
      {if $lang_update_avaiable}
        <div class="notice">
          {$lang_update_avaiable}
        </div>
      {/if}
      {if $_flash_type_}
        <div id='js-flash_message'>
          <div class='{$_flash_type_}' id='js-flash_{$_flash_type_}'
               onclick="hideDiv('js-flash_message')">
            <h4>{$_flash_headline_}</h4>
            <p>{$_flash_message_}</p>
          </div>
        </div>
      {/if}
      {$_content_}
    </div>
    {if $USER_RIGHT > 3}
      <div id="footer">
        <ul>
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
        </ul>
      </div>
    {/if}
    Heavily inspired by <a href="http://arturkim.com/">Artur Kim</a>.
    Based on <a href="http://candycms.com">CandyCMS</a> by Marco Raddatz.
    <script language='javascript' type='text/javascript'>{$_javascript_language_file_}</script>
    <script language='javascript' src='%PATH_PUBLIC%/js/core/javascript{$_compress_files_suffix_}.js' type='text/javascript'></script>
    <script type="text/javascript">
      {literal}
      var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
      document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
      try {
        var pageTracker = _gat._getTracker("UA-304730-2");
        pageTracker._trackPageview();
      } catch(err) {}
      {/literal}
    </script>
  </body>
</html>