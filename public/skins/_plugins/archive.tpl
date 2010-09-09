<div id="archive">
  <ul>
    {foreach from=$data item=data key=month}
      <li>
        <a href="#" class="month">{$month} ({$data|@count})</a>
        <ul class="elements">
        {foreach from=$data item=entry}
          <li>
            <a href="/Blog/{$entry.id}/{$entry.title_seo}">
              {$entry.title}
            </a>
          </li>
        {/foreach}
        </ul>
      </li>
    {/foreach}
  </ul>
</div>
{literal}
  <script>
    var myAccordion = new Fx.Accordion($$('.month'), $$('.elements'), {
      display: -1,
      alwaysHide: true
    });
  </script>
{/literal}