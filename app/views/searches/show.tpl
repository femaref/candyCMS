<h1>{$lang_headline}</h1>
{foreach $tables as $table}
  <h3>{$table.title}</h3>
  <ol>
    {foreach $table as $data}
      {if $data.id > 0}
        <li>
          {$data.date}
          <a href="/{$table.title}/{$data.id}/highlight/{$search}">
            {$data.title}
          </a>
        </li>
      {/if}
    {/foreach}
  </ol>
{/foreach}