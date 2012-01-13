{if !$i}
  <div class='error' title='{$lang.error.missing.entry}'>
    <h4>{$lang.error.missing.entry}</h4>
  </div>
{else}
  <div class='center'>
    {if isset($i.content)}
      <h1>{$i.content}</h1>
    {/if}
    <img src="/{$i.url}" alt="{$i.file}" width="{$i.width}" height="{$i.height}" />
  </div>
{/if}