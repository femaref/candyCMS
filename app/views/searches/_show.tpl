{foreach $tables as $table}
  <h3>{$table.title}</h3>
  <ol>
    {foreach $table as $data}
      {if $data.id > 0}
        <li>
          <a href="/{$table.section}/{$data.id}/highlight/{$search}">
            {$data.title}
          </a>,
          {$data.date}
        </li>
      {/if}
    {/foreach}
  </ol>
{/foreach}