<h1>
  {$u.full_name}
  {if $USER_RIGHT == 4}
    <a href='/user/{$_request_id_}/update'>
      <img src="%PATH_IMAGES%/spacer.png" class="icon-update" alt='{$lang_update}' />
    </a>
  {/if}
</h1>
<table>
  <tr>
    <td>
      {$lang_registered_since}
    </td>
    <td>
      {$u.date}
    </td>
    <td rowspan='4' style='vertical-align:top'>
      <a href='{$u.avatar_popup}' class="js-fancybox" title='{$u.full_name}'>
        <img alt='{$u.full_name}' src="{$u.avatar_64}" />
      </a>
    </td>
  </tr>
  <tr>
    <td>
      {$lang_last_login}
    </td>
    <td>
      {$u.last_login}
    </td>
  </tr>
  <tr>
    <td>
      {$lang_about_himself}
    </td>
    <td>
      {$u.description}
    </td>
  </tr>
  <tr>
    <td>
      {$lang_contact}
    </td>
    <td>
      <a href='/mail/{$_request_id_}/ajax' class="js-fancybox">{$lang_contact_via_mail}</a>
    </td>
  </tr>
</table>
<script src='%PATH_PUBLIC%/js/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script type="text/javascript">
  $(document).ready(function(){
    $(".js-fancybox").fancybox();
  });
</script>