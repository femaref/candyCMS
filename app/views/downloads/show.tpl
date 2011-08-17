<section id="download">
  {if $USER_RIGHT >= 3}
    <p class="center">
      <a href='/download/create'>
        <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='' width="16" height="16" />
        {$lang_create_entry_headline}
      </a>
    </p>
  {/if}
  {if !$download}
    <div class='error' id='js-error' title='{$lang_no_entries}'>
      <p>{$lang_no_entries}</p>
    </div>
  {else}
    <h1>{$lang_headline}</h1>
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
              <a href="{$f.url}">{$f.title}</a>
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
                {$f.downloads} {$lang_downloads}
              {/if}
            </td>
            <td>
              {if $USER_RIGHT >= 3}
                <a href='/download/{$f.id}/update'>
                  <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang_update}'
                    title='{$lang_update}' width="16" height="16" />
                </a>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-destroy pointer" alt='{$lang_destroy}'
                  title='{$lang_destroy}' width="16" height="16"
                  onclick="confirmDelete('/download/{$f.id}/destroy')" />
              {else}
                <a href="/download/{$f.id}">
                  <img src='%PATH_IMAGES%/spacer.png' class="icon-download" alt='{$lang_download}'
                    title='{$lang_download}' width="32" height="32" />
                </a>
              {/if}
            </td>
          </tr>
        {/foreach}
      {/foreach}
    </table>
  {/if}
</section>