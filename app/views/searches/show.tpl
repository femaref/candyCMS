{foreach from=$tables item=table}
  <h1>{$table.title}</h1>
  {foreach from=$table item=data}
    {if !$data.id}
      <div class='error' id='js-error' title='{$lang_missing_entry}' onclick="hideDiv('js-error')">
        <p>{$lang_missing_entry}</p>
      </div>
    {elseif $data.id > 0}
      <div class="element">
        <a href="/{$table.title}/{$data.id}">
          {$data.title}
        </a>
        -
        {$data.date}
      </div>
    {/if}
  {/foreach}
{/foreach}