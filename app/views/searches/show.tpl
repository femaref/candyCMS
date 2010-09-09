<div id="search">
  <h1>{$lang_headline}</h1>
  {foreach from=$tables item=table}
    <fieldset>
      <legend class="big">{$table.title}</legend>
      <ol>
        {foreach from=$table item=data}
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
