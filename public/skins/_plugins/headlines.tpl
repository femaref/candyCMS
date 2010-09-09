<div id="headlines">
  <ul>
    {foreach from=$data item=data}
      <li>
        <a href="/Blog/{$data.id}/{$data.title_seo}" title="{$data.date}">
          {$data.title}
        </a>
      </li>
    {/foreach}
  </ul>
</div>