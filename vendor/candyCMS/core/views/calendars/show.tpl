{strip}
  {if $_SESSION.user.role >= 3}
    <p class='center'>
      <a href='/{$_REQUEST.controller}/create'>
        <img src='{$_PATH.images}/candy.global/spacer.png'
            class='icon-create'
            alt='{$lang.global.create.entry}'
            width='16' height='16' />
        {$lang.global.create.entry}
      </a>
    </p>
  {/if}
  <div class='page-header'>
    <h1>
      {$lang.global.calendar}
      {if isset($_REQUEST.action) && $_REQUEST.action == 'archive'}
        -
        {$lang.global.archive}
      {else}
        &nbsp;
        <a href='/{$_REQUEST.controller}/icalfeed'>
          <img src='{$_PATH.images}/candy.global/spacer.png'
              class='icon-calendar_add js-tooltip'
              alt='{$lang.calendars.info.icsfeed}'
              title='{$lang.calendars.info.icsfeed}'
              width='16' height='16' />
        </a>
      {/if}
    </h1>
  </div>
  {if isset($_REQUEST.id)}
    <p class='center'>
      <a href='/{$_REQUEST.controller}/{$_REQUEST.id - 1}/archive' rel='prev'>
        &laquo; {$_REQUEST.id - 1}
      </a>
      &nbsp;&nbsp;
      <strong>{$_REQUEST.id}</strong>
      &nbsp;&nbsp;
      <a href='/{$_REQUEST.controller}/{$_REQUEST.id + 1}/archive' rel='next'>
        {$_REQUEST.id + 1} &raquo;
      </a>
    </p>
  {/if}
  {if !$calendar}
    <div class='alert alert-warning'>
      <h4>{$lang.error.missing.entries}</h4>
    </div>
  {else}
    {foreach $calendar as $c}
      <h2>{$c.month} {$c.year}</h2>
      <table class='table tablesorter'>
        <thead>
          <tr>
            <th class='column-date headerSortDown'>{$lang.global.date.date}</th>
            <th class='column-description'>{$lang.global.description}</th>
            <th class='column-actions'></th>
          </tr>
        </thead>
        <tbody>
          {foreach $c.dates as $d}
            <tr>
              <td>
                {$d.start_date}
                {if $d.end_date > 1}
                  &nbsp;
                  -
                  &nbsp;
                  {$d.end_date}
                {/if}
              </td>
              <td>
                <strong>
                  {$d.title}
                </strong>
                {if $d.content}
                  <br />
                  {$d.content}
                {/if}
              </td>
              <td class='center'>
                <a href='{$d.url}'>
                  <img src='{$_PATH.images}/candy.global/spacer.png'
                      class='icon-calendar_add js-tooltip'
                      alt='{$lang.calendars.info.ics}'
                      title='{$lang.calendars.info.ics}'
                      width='16' height='16' />
                </a>
                {if $_SESSION.user.role >= 3}
                  &nbsp;
                  <a href='/{$_REQUEST.controller}/{$d.id}/update'>
                    <img src='{$_PATH.images}/candy.global/spacer.png'
                        class='icon-update js-tooltip'
                        alt='{$lang.global.update.update}'
                        title='{$lang.global.update.update}'
                        width='16' height='16' />
                  </a>
                  &nbsp;
                  <a href="#" onclick="confirmDestroy('/{$_REQUEST.controller}/{$d.id}/destroy')">
                    <img src='{$_PATH.images}/candy.global/spacer.png'
                        class='icon-destroy js-tooltip'
                        alt='{$lang.global.destroy.destroy}'
                        title='{$lang.global.destroy.destroy}'
                        width='16' height='16' />
                  </a>
                {/if}
              </td>
            </tr>
          {/foreach}
        </tbody>
      </table>
    {/foreach}
  {/if}
  {if !isset($_REQUEST.action)}
    <p class='center'>
      <a href='/{$_REQUEST.controller}/{$smarty.now|date_format:'%Y'}/archive' class='btn'>
        {$lang.global.archive}
      </a>
    </p>
  {/if}
  <script type='text/javascript' src='{$_PATH.js}/core/jquery.tablesorter{$_SYSTEM.compress_files_suffix}.js'></script>
  <script type='text/javascript'>
    $('table').tablesorter();
  </script>
{/strip}