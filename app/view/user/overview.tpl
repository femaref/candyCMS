<table>
  <tr>
    <th colspan='5'>{$lang_headline}</th>
  </tr>
  {foreach from=$user item=u}
    <tr style='background:{cycle values="transparent,#eee"}'>
      <td style='width:5%'>
        <img src='%PATH_UPLOAD%/{$u.avatar32}' width="18" height="18" alt='' />
      </td>
      <td style='width:35%' class="left">
        <a href='/User/{$u.id}'>{$u.name} {$u.surname}</a>
      </td>
      <td style='width:25%' title='{$lang_registered_since}'>{$u.regdate}</td>
      <td style='width:25%' title='{$lang_last_login}'>{$u.last_login}</td>
      <td style='width:10%'>
        {if $UR == 4}
          <a href='/User/update/{$u.id}'>
            <img src='%PATH_IMAGES%/spacer.gif' class="icon-update" alt='{$lang_update}'
                 title='{$lang_update}' />
          </a>
        &nbsp;
          <img src='%PATH_IMAGES%/spacer.gif' class="icon-destroy" alt='{$lang_destroy}'
               title='{$lang_destroy}' class="pointer"
               onclick="confirmDelete('{$u.name}', '/User/destroy/{$u.id}')" />
        {/if}
      </td>
    </tr>
  {/foreach}
</table>