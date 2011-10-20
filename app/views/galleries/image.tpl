<section id="image">
  {if !$i}
    <div class='error' id='js-error' title='{$lang.error.missing.entry}'>
      <p>{$lang.error.missing.entry}</p>
    </div>
  {else}
    <img src="%PATH_UPLOAD%/gallery/{$i.album_id}/popup/{$i.file}" alt="{$i.file}" />
  {/if}
</section>