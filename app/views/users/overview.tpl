<section id="user">
  {if $USER_RIGHT == 4}
    <p class="center">
      <a href='/user/create'>
        <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='' width="16" height="16" />
        {$lang_create}
      </a>
    </p>
  {/if}
  <h1>{$lang_headline}</h1>
  <table>
    {foreach $user as $u}
      <tr>
        <td style='width:5%'>
          <img src='{$u.avatar_32}' width="18" height="18" alt='' />
        </td>
        <td style='width:35%' class="left">
          <a href='/user/{$u.id}/{$u.encoded_full_name}'>{$u.full_name}</a>
        </td>
        <td style='width:25%' title='{$lang_registered_since}'>{$u.date}</td>
        <td style='width:25%' title='{$lang_last_login}'>{$u.last_login}</td>
        <td style='width:10%'>
          {if $USER_RIGHT == 4}
            <a href='/user/{$u.id}/update'>
              <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang_update}'
                   title='{$lang_update}' width="16" height="16" />
            </a>
            <img src='%PATH_IMAGES%/spacer.png' class="icon-destroy" alt='{$lang_destroy}'
                 title='{$lang_destroy}' class="pointer" width="16" height="16"
                 onclick="confirmDelete('/user/{$u.id}/destroy')" />
          {/if}
        </td>
      </tr>
    {/foreach}
  </table>
</section>