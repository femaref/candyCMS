<section id="logs">
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
        <td style='width:20%'>
          {$l.time_start}
        </td>
        <td style='width:20%'>
          {$l.time_end}
        </td>
        <td style='width:10%'>
          {if $USER_RIGHT == 4}
            <img src='%PATH_IMAGES%/spacer.png' class="icon-destroy" alt='{$lang.global.destroy.destroy}'
                 title='{$lang.global.destroy.destroy}' class="pointer" width="16" height="16"
                 onclick="confirmDelete('/log/{$l.id}/destroy')" />
          {/if}
        </td>
      </tr>
    {/foreach}
  </table>
  {$_pages_}
  <script src='%PATH_PUBLIC%/js/core/jquery.infiniteScroll{$_compress_files_suffix_}.js' type='text/javascript'></script>
  <script type="text/javascript">
    $(document).ready(function(){
      $('table').infinitescroll({
        navSelector  : "section.pages",
        nextSelector : "section.pages a:first",
        itemSelector : "table tr",
        loading : { msgText : '', img: "%PATH_IMAGES%/loading.gif", loadingText  : '', finishedMsg  : '' }
      });
    });
  </script>
</section>