<div id="archive">
  <ul>
    {foreach from=$data item=d key=month}
      <li>
        <a href="#" class="js-archive_month">{$month} ({$data|@count})</a>
        <ul class="js-archive_entries">
        {foreach from=$d item=entry}
          <li>
            <a href="/Blog/{$entry.id}/{$entry.title_seo}" title="{$entry.date}">
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
  <script type="text/javascript">
    var myAccordion = new Fx.Accordion($$('.js-archive_month'), $$('.js-archive_entries'), {
      display: -1,
      alwaysHide: true
    });
  </script>
{/literal}