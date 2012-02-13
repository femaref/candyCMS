{if $USER_ROLE == 4}
  <p class='center'>
    <a href='/user/create'>
      <img src='%PATH_IMAGES%/spacer.png' class='icon-create' alt='' width='16' height='16' />
      {$lang.user.title.create}
    </a>
  </p>
{/if}
<div class='page-header'>
  <h1>{$lang.user.title.overview}</h1>
</div>
<table class='table'>
  <thead>
    <tr>
      <th class='headerSortDown'>{$lang.global.id}</th>
      <th></th>
      <th>{$lang.global.name}</th>
      <th class='center'>{$lang.user.label.registered_since}</th>
      <th class='center'>{$lang.user.label.last_login}</th>
      <th class='center'>{$lang.global.newsletter}</th>
      {if $USER_ROLE == 4}
        <th></th>
      {/if}
    </tr>
  </thead>
  {foreach $user as $u}
    <tr>
      <td>{$u.id}</td>
      <td>
        <img src='{$u.avatar_32}' width='20' height='20' alt='' />
      </td>
      <td>
        <a href='/user/{$u.id}/{$u.encoded_full_name}'>{$u.full_name}</a>
        <br />
        {if $u.role == 1}
          ({$lang.global.user.roles.1})
        {elseif $u.role == 2}
          ({$lang.global.user.roles.2})
        {elseif $u.role == 3}
          ({$lang.global.user.roles.3})
        {elseif $u.role == 4}
          ({$lang.global.user.roles.4})
        {/if}
      </td>
      <td class='center'>
        {if $u.verification_code !== ''}
          <span style='text-decoration:line-through'>{$u.date}</span>
        {else}
          {$u.date}
        {/if}
      </td>
      <td class='center'>
        {$u.last_login}
      </td>
      <td class='center'>
        <img src='%PATH_IMAGES%/spacer.png'
             class='icon-{if $u.receive_newsletter == 1}success{else}close{/if}'
             alt='{if $u.receive_newsletter == 1}✔{else}✖{/if}' width='16'
             height='16' title='{if $u.receive_newsletter == 1}✔{else}✖{/if}' />
      </td>
      {if $USER_ROLE == 4}
        <td class='center'>
          <a href='/user/{$u.id}/update'>
            <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang.global.update.update}'
                 title='{$lang.global.update.update}' width="16" height="16" />
          </a>
          <a href="#" onclick="candy.system.confirmDestroy('/user/{$u.id}/destroy')">
            <img src='%PATH_IMAGES%/spacer.png' class="icon-destroy" alt='{$lang.global.destroy.destroy}'
                 title='{$lang.global.destroy.destroy}' width="16" height="16"  />
          </a>
        </td>
      {/if}
    </tr>
  {/foreach}
</table>
<script type='text/javascript' src='%PATH_JS%/core/jquery.tablesorter{$_compress_files_suffix_}.js'></script>
<script type='text/javascript'>
  $('table').tablesorter();
</script>