<div class='page-header'>
  <h1>{$lang.global.search}</h1>
</div>
{if $tables.blogs|@count > 2 || $tables.contents|@count > 2 || $tables.downloads|@count > 2 || $tables.gallery_albums|@count > 2}
  {foreach $tables as $table}
    {if $table|@count > 2}
    <h3>{$table.title}</h3>
    <ol>
      {foreach $table as $data}
        {if $data.id > 0}
          <li>
            <a href="/{$table.section}/{$data.id}/highlight/{$string}">
              {$data.title}
            </a>,
            {$data.datetime}
          </li>
        {/if}
      {/foreach}
    </ol>
    {/if}
  {/foreach}
{else}
  <p>
    {$lang.search.info.fail|replace:'%b':$string}
  </p>
{/if}