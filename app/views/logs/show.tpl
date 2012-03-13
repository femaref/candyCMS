{strip}
  <div class='page-header'>
    <h1>{$lang.global.logs}</h1>
  </div>
  <table class='table'>
    <thead>
      <tr>
        <th class='column-author'>{$lang.global.author}</th>
        <th class='column-section'>{$lang.global.section}</th>
        <th class='column-action'>{$lang.global.action}</th>
        <th class='column-id center'>{$lang.global.id}</th>
        <th class='column-date headerSortDown'>{$lang.global.date.date}</th>
        <th class='column-actions'></th>
      </tr>
    </thead>
    {foreach $logs as $l}
      {if $l.action_name == 'create'}
        <tr style='color:green'>
      {elseif $l.action_name == 'update'}
        <tr style='color:blue'>
      {elseif $l.action_name == 'destroy'}
        <tr style='color:red'>
      {else}
        <tr>
      {/if}
        <td class='left'>
          <a href='/user/{$l.uid}'>{$l.full_name}</a>
        </td>
        <td>
          {$l.section_name}
        </td>
        <td>
          {$l.action_name}
        </td>
        <td class='center'>
          {$l.action_id}
        </td>
        <td>
          {$l.time_start}
          {if $l.time_start !== $l.time_end}
            -
            {$l.time_end}
          {/if}
        </td>
        <td class='center'>
          <a href="#" onclick="confirmDestroy('/log/{$l.id}/destroy')">
            <img src='{$_PATH.images}/candy.global/spacer.png'
                class='icon-destroy js-tooltip'
                alt='{$lang.global.destroy.destroy}'
                title='{$lang.global.destroy.destroy}'
                width='16' height='16' />
          </a>
        </td>
      </tr>
    {/foreach}
  </table>
  {$_pages_}
  <script src='{$_PATH.js}/core/jquery.infiniteScroll{$_SYSTEM.compress_files_suffix}.js' type='text/javascript'></script>
  <script type='text/javascript' src='{$_PATH.js}/core/jquery.tablesorter{$_SYSTEM.compress_files_suffix}.js'></script>
  <script type='text/javascript'>
    $(document).ready(function(){
      $('table').infinitescroll({
        navSelector  : 'div.pagination',
        nextSelector : 'div.pagination a:first',
        itemSelector : 'table tbody tr',
        loading : { msgText : '', img: '{$_PATH.images}/candy.global/loading.gif', loadingText  : '', finishedMsg  : '' }
      });
    });

    $('table').tablesorter();
  </script>
{/strip}