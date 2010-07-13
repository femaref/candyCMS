<table>
  <tr>
    <th colspan='4'>{$lang_headline}</th>
  </tr>
	{foreach from=$user item=u}
      <tr style='background:{cycle values="transparent,transparent"}'>
        <td style='width:5%'>
          <img src='%PATH_UPLOAD%/{$u.avatar18}' width='18' height='18' alt='' />
        </td>
        <td style='text-align:left;width:35%'>
          <a href='/User/{$u.id}'>{$u.name} {$u.surname}</a>
        </td>
        <td style='width:25%' title='{$lang_registered_since}'>{$u.regdate}</td>
        <td style='width:25%' title='{$lang_last_login}'>{$u.last_login}</td>
        <td style='width:10%'>
          {if $UR == 4}
            <a href='/User/edit/{$u.id}'>
              <img src='%PATH_IMAGES%/icons/update.png' alt='{$lang_update}'
                   title='{$lang_update}' />
            </a>
            <img src='%PATH_IMAGES%/icons/destroy.png' alt='{$lang_destroy}'
                 title='{$lang_destroy}'
                 onclick="confirmDelete('{$u.name}', '/User/destroy/{$u.id}')" />
          {/if}
        </td>
      </tr>
	{/foreach}
</table>