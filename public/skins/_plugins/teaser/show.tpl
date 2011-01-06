<div class="element">
  {foreach from=$data item=d}
    <a href="/blog/{$d.id}/{$d.encoded_title}" title="{$d.date}">
      {$d.title}
    </a>
    {$d.teaser}
  {/foreach}
</div>