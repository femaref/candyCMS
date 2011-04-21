<h1>
  {$user.name} {$user.surname}
  {if $USER_RIGHT == 4}
    <a href='/user/{$uid}/update'>
      <img src="%PATH_IMAGES%/spacer.png" class="icon-update" alt='{$lang_update}' />
    </a>
  {/if}
</h1>
<table>
  <tr class='row1'>
    <td class='td_left'>
      {$lang_registered_since}
    </td>
    <td class='td_right'>
      {$date}
    </td>
    <td rowspan='4' style='vertical-align:top'>
      <a href='{$avatar_popup}' class="js-fancybox" title='{$user.name} {$user.surname}'>
        <img class='image' alt='{$user.name}' src="{$avatar_64}" />
      </a>
    </td>
  </tr>
  <tr class='row2'>
    <td class='td_left'>
      {$lang_last_login}
    </td>
    <td class='td_right'>
      {$last_login}
    </td>
  </tr>
  <tr class='row1'>
    <td class='td_left'>
      {$lang_about_himself}
    </td>
    <td class='td_right'>
      {$user.description}
    </td>
  </tr>
  <tr class='row2'>
    <td class='td_left'>
      {$lang_contact}
    </td>
    <td class='td_right'>
      <a href='/mail/{$uid}/ajax' class="js-fancybox">{$lang_contact_via_mail}</a>
    </td>
  </tr>
</table>
<script language='javascript' src='%PATH_PUBLIC%/js/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script language='javascript' type="text/javascript">
  $(document).ready(function(){
    $(".js-fancybox").fancybox();
  });
</script>