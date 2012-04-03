{strip}
  <section id='archive'>
    <ul>
      {foreach from=$data item=d key=month}
        <li>
          <a href='#{$month}' name='archive-{$month}' class='js-archive_month'>{$month} ({$d|@count})</a>
          <ul style='display:none'>
          {foreach from=$d item=entry}
            <li>
              <a href='{$entry.url}' title='{$entry.date}' class='js-tooltip'>
                {$entry.title}
              </a>
            </li>
          {/foreach}
          </ul>
        </li>
      {/foreach}
    </ul>
  </section>
  <script type='text/javascript'>
    if($('.js-archive_month')) {
      $('.js-archive_month').click(function(){
        $(this).next().toggle();
      });
    };
  </script>
{/strip}