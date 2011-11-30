{if $USER_RIGHT >= 3}
  <p class="center">
    <a href='/calendar/create'>
      <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='' width="16" height="16" />
      {$lang.global.create.entry}
    </a>
  </p>
{/if}
<h1>{$lang.global.calendar}</h1>
<p class="center">
  {if isset($smarty.get.page) && $smarty.get.page > 1}
    <a href="{$smarty.get.page - 1}" rel="prev">&laquo; {$smarty.get.page - 1}</a>&nbsp;&nbsp;
    <strong>{$smarty.get.page}</strong>
    &nbsp;&nbsp;<a href="{$smarty.get.page + 1}" rel="next">{$smarty.get.page + 1} &raquo;</a>
  {/if}
</p>
{if !$calendar}
  <div class='error' title='{$lang.error.missing.entries}'>
    <h4>{$lang.error.missing.entries}</h4>
  </div>
{else}
  <table>
    {foreach $calendar as $c}
      <tr>
        <th colspan="4">
          <h2>{$c.month} {$c.year}</h2>
        </th>
      </tr>
      {foreach $c.dates as $d}
        <tr class='{cycle values="row1,row2"}'>
          <td style="width:30%">
            {$d.start_date}
            {if $d.end_date > 1}
              -
              {$d.end_date}
            {/if}
          </td>
          <td style="width:60%">
            <h3>
              {$d.title}
            </h3>
            {if $d.content !== ''}
              {$d.content}
            {/if}
          </td>
          <td style="width:10%">
            {if $USER_RIGHT >= 3}
              <a href='/calendar/{$d.id}/update'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang.global.update.update}'
                  title='{$lang.global.update.update}' width="16" height="16" />
              </a>
              <img src='%PATH_IMAGES%/spacer.png' class="icon-destroy pointer" alt='{$lang.global.destroy.destroy}'
                title='{$lang.global.destroy.destroy}' width="16" height="16"
                onclick="candy.system.confirmDestroy('/calendar/{$d.id}/destroy')" />
            {/if}
          </td>
        </tr>
      {/foreach}
    {/foreach}
  </table>
{/if}
{if !isset($smarty.get.action)}
  <p class="center">
    <a href="/calendar/archive/2011">{$lang.global.archive}</a>
  </p>
{/if}