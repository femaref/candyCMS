<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv='content-type' content='text/html;charset=utf-8' />
    <meta name='description' content='{$meta_description}' />

    <link href='{$url}/RSS/blog' rel='alternate' type='application/rss+xml' title='RSS' />
      {if $dev == true}
        <link href='%PATH_CSS%/style.css' rel='stylesheet' type='text/css' media='screen, projection' />
        <script language='javascript' src='%PATH_PUBLIC%/js/mootools.js' type='text/javascript'></script>
      {else}
        <link href='%PATH_CSS%/style-min.css' rel='stylesheet' type='text/css' media='screen, projection' />
        <script language='javascript' src='%PATH_PUBLIC%/js/mootools-min.js' type='text/javascript'></script>
      {/if}
    <title>{$title}</title>
  </head>
  <body>
    <div id='container'>
      <div id='navigation'>
        {if $uid > 0}
          <strong>{$lang_welcome} <a href='/User/{$uid}'>{$user}</a>!</strong> &middot;
        {/if}
          <a href='/Blog'>{$lang_blog}</a> &middot;
          <a href='/Gallery'>{$lang_gallery}</a> &middot;
        {if $uid == 0}
          <a href='/Login'>{$lang_login}</a> &middot;
          <a href='/Register'>{$lang_register}</a>
        {else}
          <a href='/User/Settings'>{$lang_settings}</a> &middot;
          <a href='/Logout'>{$lang_logout}</a>
        {/if}
      </div>
      <div id='body'>
        <div id='flashMessage'>
          <div class='%FLASH_TYPE% tooltip' id='%FLASH_TYPE%'
            title="::" onclick="hideDiv('flashMessage')">
            <h4>%FLASH_HEADLINE%</h4>
            <p>%FLASH_MESSAGE%</p>
          </div>
        </div>