<div id="search">
  <h1>{$lang_headline}</h1>
  {foreach $tables as $table}
    <fieldset>
      <legend class="big">{$table.title}</legend>
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
    </fieldset>
  {/foreach}
</div>
