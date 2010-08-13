<table>
  <tr>
    <th colspan='3'>
      {$user.name} {$user.surname}
      {if $USER_RIGHT == 4}
        <a href='/User/update/{$USER_ID}'>
          <img src="%PATH_IMAGES%/spacer.gif" class="icon-update" alt='{$lang_update}' style='vertical-align:baseline' />
        </a>
      {/if}
    </th>
  </tr>
  <tr class='row1'>
    <td class='td_left'>
      {$lang_registered_since}
    </td>
    <td class='td_right'>
      {$regdate}
    </td>
    <td rowspan='4' style='vertical-align:top'>
      <a href='{$avatar_popup}' rel='lightbox'
         title='{$user.name} ({$avatar_popup_info[0]} x {$avatar_popup_info[1]} px)'>
          <img class='image' alt='{$user.name}' src="{$avatar_100}" {$avatar_thumb_info[3]} />
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
      <a href='/Mail/{$USER_ID}'>Klicke hier, um {$user.name} eine E-Mail zu schicken!</a>
    </td>
  </tr>
</table>
{literal}
  <script language='javascript' src='%PATH_PUBLIC%/js/slimbox-min.js' type='text/javascript'></script>
{/literal}