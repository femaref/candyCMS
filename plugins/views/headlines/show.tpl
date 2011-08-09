<div id="headlines">
  <ul>
    {foreach from=$data item=d}
      <li>
        <a href="/blog/{$d.id}/{$d.encoded_title}" title="{$d.date}">
          {$d.title}
        </a>
      </li>
    {/foreach}
  </ul>
</div>