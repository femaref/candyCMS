{strip}
  {if !$i}
    <div class='alert alert-warning'>
      <h4>{$lang.error.missing.entry}</h4>
    </div>
  {else}
    <div class='center'>
      {if isset($i.content)}
        <div class='page-header'>
          <h1>{$i.content}</h1>
        </div>
      {/if}
      <img src='{$i.url}' alt='{$i.file}' width='{$i.width}' height='{$i.height}' />
    </div>
  {/if}
{/strip}