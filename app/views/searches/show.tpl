<section id="search">
  <h1>{$lang_headline}</h1>
  {foreach $tables as $table}
    <h2>{$table.title}</h2>
    <ol>
      {foreach $table as $data}
        {if $data.id > 0}
          <li>
            <a href="/{$table.title}/{$data.id}/highlight/{$search}">
              {$data.title}
            </a>,
            {$data.date}
          </li>
        {/if}
      {/foreach}
    </ol>
  {/foreach}
</section>
