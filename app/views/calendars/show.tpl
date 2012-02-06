{if $USER_ROLE >= 3}
  <p class="center">
    <a href='/calendar/create'>
      <i class="icon icon-create"></i>
      {$lang.global.create.entry}
    </a>
  </p>
{/if}
<div class='page-header'>
  <h1>
    {$lang.global.calendar}
    {if isset($smarty.get.action) && $smarty.get.action == 'archive'}
      -
      {$lang.global.archive}
    {/if}
  </h1>
</div>
<p class="center">
  {if isset($smarty.get.page) && $smarty.get.page > 1}
    <a href="{$smarty.get.page - 1}" rel="prev">&laquo; {$smarty.get.page - 1}</a>&nbsp;&nbsp;
    <strong>{$smarty.get.page}</strong>
    &nbsp;&nbsp;<a href="{$smarty.get.page + 1}" rel="next">{$smarty.get.page + 1} &raquo;</a>
  {/if}
</p>
{if !$calendar}
  <div class='alert error' title='{$lang.error.missing.entries}'>
    <h4>{$lang.error.missing.entries}</h4>
  </div>
{else}
  {foreach $calendar as $c}
    <h2>{$c.month} {$c.year}</h2>
    <table class="table tablesorter">
      <thead>
        <tr>
          <th width="20%" class="headerSortDown">{$lang.global.date.date}</th>
          <th width="70%">{$lang.global.description}</th>
          <th width="10%"></th>
        </tr>
      </thead>
      <tbody>
        {foreach $c.dates as $d}
          <tr>
            <td style="width:25%">
              {$d.start_date}
              {if $d.end_date > 1}
                -
                {$d.end_date}
              {/if}
            </td>
            <td style="width:65%">
              <strong>
                {$d.title}
              </strong>
              <br />
              {if $d.content !== ''}
                {$d.content}
              {/if}
            </td>
            <td style="width:10%">
              {if $USER_ROLE >= 3}
                <a href='/calendar/{$d.id}/update'>
                  <i class="icon icon-update"></i>
                </a>
                <a href="#" onclick="candy.system.confirmDestroy('/calendar/{$d.id}/destroy')">
                  <i class="icon icon-destroy"></i>
                </a>
              {/if}
            </td>
          </tr>
        {/foreach}
      </tbody>
    </table>
  {/foreach}
{/if}
{if !isset($smarty.get.action)}
  <p class="center">
    <a href="/calendar/archive/{$smarty.now|date_format:'%Y'}" class='btn'>
      {$lang.global.archive}
    </a>
  </p>
{/if}
<script type='text/javascript' src='%PATH_JS%/core/jquery.tablesorter{$_compress_files_suffix_}.js'></script>
<script type='text/javascript'>
  $('table').tablesorter();
</script>