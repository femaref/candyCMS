{if $USER_ROLE >= 3}
  <p class="center">
    <a href='/content/create'>
      <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='' width="16" height="16" />
      {$lang.content.title.create}
    </a>
  </p>
{/if}
<h1>{$lang.global.contents}</h1>
<table>
  <tr>
    <th class="left">{$lang.global.title}</th>
    <th>{$lang.global.date.date}</th>
    <th>{$lang.global.author}</th>
    <th>{$lang.global.published}</th>
    <th></th>
  </tr>
  {foreach $content as $c}
    <tr class='{cycle values="row1,row2"}'>
      <td class="left">
        <a href='/content/{$c.id}/{$c.encoded_title}'>
          {$c.title}
        </a>
      </td>
      <td>{$c.datetime}</td>
      <td>
        <a href='/user/{$c.author_id}'>
          {$c.name} {$c.surname}
        </a>
      </td>
      <td>
        <img src='%PATH_IMAGES%/spacer.png'
             class="icon-{if $c.published == true}success{else}close{/if}"
             alt='{$lang.global.published}' title="" width="16" height="16" />
      </td>
      {if $USER_ROLE >= 3}
        <td>
          <a href='/content/{$c.id}/update'>
            <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang.global.update.update}'
              title='{$lang.global.update.update}' width="16" height="16" />
          </a>
          <a href='#' onclick="candy.system.confirmDestroy('/content/{$c.id}/destroy')">
            <img src='%PATH_IMAGES%/spacer.png' class="icon-destroy pointer" alt='{$lang.global.destroy.destroy}'
              title='{$lang.global.destroy.destroy}' width="16" height="16" />
          </a>
        </td>
      {/if}
    </tr>
  {/foreach}
</table>