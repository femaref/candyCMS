<h1>{$lang.global.logs}</h1>
<table id="js-logs">
  {foreach $logs as $l}
    <tr>
      <td style='width:25%' class="left">
        <a href='/user/{$l.uid}'>{$l.full_name}</a>
      </td>
      <td style='width:10%' class="left">
        {$l.section_name}
      </td>
      <td style='width:10%' class="left">
        {$l.action_name}
      </td>
      <td style='width:5%'>
        {$l.action_id}
      </td>
      <td style='width:40%'>
        {$l.time_start}
        {if $l.time_start !== $l.time_end}
          -
          {$l.time_end}
        {/if}
      </td>
      <td style='width:10%'>
        {if $USER_RIGHT == 4}
          <a href="#" onclick="candy.system.confirmDestroy('/log/{$l.id}/destroy')">
            <img src='%PATH_IMAGES%/spacer.png' class="icon-destroy" alt='{$lang.global.destroy.destroy}'
                 title='{$lang.global.destroy.destroy}' width="16" height="16" />
          </a>
        {/if}
      </td>
    </tr>
  {/foreach}
</table>
{$_pages_}
<script src='%PATH_JS%/core/jquery.infiniteScroll{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script type="text/javascript">
  $(document).ready(function(){
    $('table').infinitescroll({
      navSelector  : "div.pages",
      nextSelector : "div.pages a:first",
      itemSelector : "table tr",
      loading : { msgText : '', img: "%PATH_IMAGES%/loading.gif", loadingText  : '', finishedMsg  : '' }
    });
  });
</script>