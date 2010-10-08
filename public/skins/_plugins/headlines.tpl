<div id="headlines">
  <ul>
    {foreach from=$data item=d}
      <li>
        <a href="/Blog/{$d.id}/{$d.title_seo}" title="{$d.date}">
          {$d.title}
        </a>
      </li>
    {/foreach}
  </ul>
</div>