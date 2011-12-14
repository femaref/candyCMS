{foreach $user as $u}
  <h1>
    {$u.full_name}
    {if $USER_RIGHT == 4 || $u.id == $USER_ID}
      <a href='/user/{$_request_id_}/update'>
        <img src="%PATH_IMAGES%/spacer.png" class="icon-update" alt='{$lang.global.update}' width="16" height="16" />
      </a>
    {/if}
  </h1>
  <table class="user">
    <tr>
      <td>
        {$lang.user.label.registered_since}
      </td>
      <td>
        {$u.date}
      </td>
      <td rowspan='4' style='vertical-align:top'>
        <a href='{$u.avatar_popup}' class="js-fancybox" title='{$u.full_name}'>
          <img alt='{$u.full_name}' src="{$u.avatar_100}" />
        </a>
      </td>
    </tr>
    <tr>
      <td>
        {$lang.user.label.last_login}
      </td>
      <td>
        {$u.last_login}
      </td>
    </tr>
    <tr>
      <td>
        {$lang.user.label.content.show|replace:'%u':$u.name}
      </td>
      <td>
        {$u.content}
      </td>
    </tr>
    <tr>
      <td>
        {$lang.global.contact}
      </td>
      <td>
        {* Absolute URL due to fancybox bug *}
        <a href='{$WEBSITE_URL}/mail/{$_request_id_}/ajax' class="js-fancybox">
          {$lang.user.contact_via_email|replace:'%u':$u.name}
        </a>
      </td>
    </tr>
  </table>
{/foreach}
<script src='%PATH_JS%/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script type="text/javascript">
  $(document).ready(function(){
    $(".js-fancybox").fancybox();
  });
</script>