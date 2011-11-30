{if $USER_RIGHT >= 3}
  <p class="center">
    <a href='/download/create'>
      <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='' width="16" height="16" />
      {$lang.global.create.entry}
    </a>
  </p>
{/if}
<h1>{$lang.global.downloads}</h1>
{if !$download}
  <div class='error' title='{$lang.error.missing.entries}'>
    <h4>{$lang.error.missing.entries}</h4>
  </div>
{else}
  <table>
    {foreach $download as $d}
      <tr>
        <th colspan="5">
          <h2>{$d.category}</h2>
        </th>
      </tr>
      {foreach $d.files as $f}
        <tr class='{cycle values="row1,row2"}'>
          <td>
            <img src='%PATH_IMAGES%/files/{$f.extension}.png'
                 width='32' height='32' alt='{$f.extension}' />
          </td>
          <td>
            <a href="{$f.url}" target="_blank">{$f.title}</a>
            {if $f.content !== ''}
              <br />
              {$f.content}
            {/if}
          </td>
          <td>{$f.date}</td>
          <td>
            {$f.size}
            {if $USER_RIGHT >= 3}
              <br />
              {$f.downloads} {$lang.global.downloads}
            {/if}
          </td>
          <td>
            {if $USER_RIGHT >= 3}
              <a href='/download/{$f.id}/update'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang.global.update.update}'
                  title='{$lang.global.update.update}' width="16" height="16" />
              </a>
              <img src='%PATH_IMAGES%/spacer.png' class="icon-destroy pointer" alt='{$lang.global.destroy.destroy}'
                title='{$lang.global.destroy.destroy}' width="16" height="16"
                onclick="candy.system.confirmDestroy('/download/{$f.id}/destroy')" />
            {else}
              <a href="{$f.url}" target="_blank">
                <img src='%PATH_IMAGES%/spacer.png' class="icon-download" alt='{$lang.global.download}'
                  title='{$lang.global.download}' width="32" height="32" />
              </a>
            {/if}
          </td>
        </tr>
      {/foreach}
    {/foreach}
  </table>
{/if}