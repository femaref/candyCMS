      </div>
    </div>
    <p style='clear:both'>
      <a href='/About'>{$lang_about} {$name}</a> &middot; <a href='/Disclaimer'>{$lang_disclaimer}</a> &middot; <a href='/Contact/Bugreport'>{$lang_report_error}</a>
    </p>
    {if $UR > 3}
      <a href='/Newsletter/create' title='{$lang_newsletter_send}'>
        <img src='%PATH_IMAGES%/spacer.gif' class="icon-email" alt='' />
        {$lang_newsletter_send}
      </a> &middot;
      <a href='/Media' title='{$lang_filemanager}'>
        <img src='%PATH_IMAGES%/spacer.gif' class="icon-folder" alt='' />
        {$lang_filemanager}
      </a> &middot;
      <a href='/Content' title='{$lang_contentmanager}'>
        <img src='%PATH_IMAGES%/spacer.gif' class="icon-manager" alt='' />
        {$lang_contentmanager}
      </a> &middot;
      <a href='/User' title='{$lang_usermanager}'>
        <img src='%PATH_IMAGES%/spacer.gif' class="icon-user" alt='' />
        {$lang_usermanager}
      </a>
    {else}
      <a href='/Newsletter' title='{$lang_newsletter_create_destroy}'>
        <img src='%PATH_IMAGES%/spacer.gif' class="icon-email" alt='' />
        {$lang_newsletter_create_destroy}
      </a>
    {/if}
    {if $dev == true}
      <script language='javascript' src='%PATH_PUBLIC%/js/javascript.js' type='text/javascript'></script>
    {else}
      <script language='javascript' src='%PATH_PUBLIC%/js/javascript-min.js' type='text/javascript'></script>
    {/if}
  </body>
</html>