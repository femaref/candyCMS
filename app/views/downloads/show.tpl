{strip}
  {if $_SESSION.user.role >= 3}
    <p class='center'>
      <a href='/download/create'>
        <img src='{$_PATH.images}/candy.global/spacer.png'
            class='icon-create'
            alt='{$lang.global.create.entry}'
            width='16' height='16' />
        {$lang.global.create.entry}
      </a>
    </p>
  {/if}
  <div class='page-header'>
    <h1>{$lang.global.downloads}</h1>
  </div>
  {if !$download}
    <div class='alert alert-warning'>
      <h4>{$lang.error.missing.entries}</h4>
    </div>
  {else}
    {foreach $download as $d}
      <h2>{$d.category}</h2>
      <table class='table tablesorter'>
        <thead>
          <tr>
            <th class='column-file'></th>
            <th class='column-title headerSortDown'>{$lang.global.title}</th>
            <th class='column-date'>{$lang.global.date.date}</th>
            <th class='column-size'>{$lang.global.size}</th>
            <th class='column-actions'></th>
          </tr>
        </thead>
        <tbody>
        {foreach $d.files as $f}
          <tr>
            <td class='center'>
              <img src='{$_PATH.images}/candy.files/{$f.extension}.png'
                  width='32' height='32' alt='{$f.extension}' />
            </td>
            <td class='left'>
              <a href='{$f.url}' target='_blank'>{$f.title}</a>
              {if $f.content !== ''}
                <br />
                {$f.content}
              {/if}
            </td>
            <td>{$f.date}</td>
            <td>
              {$f.size}
              {if $_SESSION.user.role >= 3}
                <br />
                {$f.downloads} {$lang.global.downloads}
              {/if}
            </td>
            <td class='center'>
              {if $_SESSION.user.role >= 3}
                <a href='/download/{$f.id}/update'>
                  <img src='{$_PATH.images}/candy.global/spacer.png'
                      class='icon-update js-tooltip'
                      alt='{$lang.global.update.update}'
                      title='{$lang.global.update.update}'
                      width='16' height='16' />
                </a>
                &nbsp;
                <a href="#" onclick="confirmDestroy('/download/{$f.id}/destroy')">
                  <img src='{$_PATH.images}/candy.global/spacer.png'
                      class='icon-destroy js-tooltip'
                      alt='{$lang.global.destroy.destroy}'
                      title='{$lang.global.destroy.destroy}'
                      width='16' height='16' />
                </a>
              {else}
                <a href='{$f.url}'>
                  <img src='{$_PATH.images}/candy.global/spacer.png'
                       class="icon-download js-tooltip"
                       alt='{$lang.global.download}'
                       title='{$lang.global.download}'
                       width="32" height="32" />
                </a>
              {/if}
            </td>
          </tr>
        {/foreach}
        </tbody>
      </table>
    {/foreach}
  {/if}
  <script type='text/javascript' src='{$_PATH.js}/core/jquery.tablesorter{$_SYSTEM.compress_files_suffix}.js'></script>
  <script type='text/javascript'>
    $('table').tablesorter();
  </script>
{/strip}