{strip}
  {if $USER_ROLE >= 3}
    <p class='center'>
      <a href='/content/create'>
        <img src='%PATH_IMAGES%/spacer.png'
            class='icon-create'
            alt='{$lang.global.create.entry}'
            width='16' height='16' />
        {$lang.content.title.create}
      </a>
    </p>
  {/if}
  <div class='page-header'>
    <h1>{$lang.global.contents}</h1>
  </div>
  <table class='table'>
    <thead>
      <tr>
        <th class='headerSortDown'>#</th>
        <th>{$lang.global.title}</th>
        <th>{$lang.global.date.date}</th>
        <th>{$lang.global.author}</th>
        {if $USER_ROLE >= 3}
          <th class='center'>{$lang.global.published}</th>
          <th></th>
        {/if}
      </tr>
    </thead>
    {foreach $content as $c}
      <tr>
        <td>{$c.id}</td>
        <td>
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
        {if $USER_ROLE >= 3}
          <td class='center'>
            <img src='%PATH_IMAGES%/spacer.png'
                class='icon-{if $c.published == true}success{else}close{/if}'
                alt='{if $c.published == true}✔{else}✖{/if}' height='16'
                title='{if $c.published == true}✔{else}✖{/if}' width='16' />
          </td>
          <td>
            <a href='/content/{$c.id}/update'>
              <img src='%PATH_IMAGES%/spacer.png'
                  class='icon-update js-tooltip'
                  alt='{$lang.global.update.update}'
                  title='{$lang.global.update.update}'
                  width='16' height='16' />
            </a>
            &nbsp;
            <a href='#' onclick="confirmDestroy('/content/{$c.id}/destroy')">
              <img src='%PATH_IMAGES%/spacer.png'
                  class='icon-destroy js-tooltip'
                  alt='{$lang.global.destroy.destroy}'
                  title='{$lang.global.destroy.destroy}'
                  width='16' height='16' />
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
{/strip}