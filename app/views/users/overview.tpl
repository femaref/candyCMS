{if $USER_RIGHT == 4}
  <p class="center">
    <a href='/user/create'>
      <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='' width="16" height="16" />
      {$lang.global.create.entry}
    </a>
  </p>
{/if}
<h1>{$lang.user.title.overview}</h1>
<table>
  <tr>
    <th></th>
    <th class="left">{$lang.global.name}</th>
    <th>{$lang.user.label.registered_since}</th>
    <th>{$lang.user.label.last_login}</th>
    <th>{$lang.global.newsletter}</th>
    <th></th>
  </tr>
  {foreach $user as $u}
    <tr>
      <td style='width:5%'>
        <img src='{$u.avatar_32}' width="20" height="20" alt='' />
      </td>
      <td style='width:30%' class="left">
        <a href='/user/{$u.id}/{$u.encoded_full_name}'>{$u.full_name}</a>
        <br />
        {if $u.user_right == 2}
          ({$lang.global.user.rights.2})
        {elseif $u.user_right == 3}
          ({$lang.global.user.rights.3})
        {elseif $u.user_right == 4}
          ({$lang.global.user.rights.4})
        {/if}
      </td>
      <td style='width:25%'>
        {if $u.verification_code !== ''}
          <span style="text-decoration:line-through">{$u.date}</span>
        {else}
          {$u.date}
        {/if}
      </td>
      <td style='width:25%'>
        {$u.last_login}
      </td>
      <td style='width:5%'>
        <img src='%PATH_IMAGES%/spacer.png'
             class="icon-{if $u.receive_newsletter == 1}success{else}close{/if}"
             alt='{$u.receive_newsletter}' title="" width="16" height="16" />
      </td>
      <td style='width:10%'>
        {if $USER_RIGHT == 4}
          <a href='/user/{$u.id}/update'>
            <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang.global.update.update}'
                 title='{$lang.global.update.update}' width="16" height="16" />
          </a>
          <a href="#" onclick="candy.system.confirmDestroy('/user/{$u.id}/destroy')">
            <img src='%PATH_IMAGES%/spacer.png' class="icon-destroy" alt='{$lang.global.destroy.destroy}'
                 title='{$lang.global.destroy.destroy}' width="16" height="16"  />
          </a>
        {/if}
      </td>
    </tr>
  {/foreach}
</table>