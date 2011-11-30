{if !$i}
  <div class='error' title='{$lang.error.missing.entry}'>
    <h4>{$lang.error.missing.entry}</h4>
  </div>
{else}
  <img src="%PATH_UPLOAD%/gallery/{$i.album_id}/popup/{$i.file}" alt="{$i.file}" />
{/if}