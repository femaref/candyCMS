{if !$i}
  <div class='error' title='{$lang.error.missing.entry}'>
    <h4>{$lang.error.missing.entry}</h4>
  </div>
{else}
  <h1>{$i.file}</h1>
  {if isset($i.content)}
    <h3>{$i.content}</h3>
  {/if}
  <img src="/{$i.url}" alt="{$i.file}"
       width="{$i.width}" height="{$i.height}" />
{/if}