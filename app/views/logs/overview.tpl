<section id="log">
  <h1>{$lang_headline}</h1>
  <div id="js-logs">
    <table>
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
              <img src='%PATH_IMAGES%/spacer.png' class="icon-destroy" alt='{$lang_destroy}'
                   title='{$lang_destroy}' class="pointer"
                   onclick="confirmDelete('/log/{$l.id}/destroy')" />
            {/if}
          </td>
        </tr>
      {/foreach}
    </table>
  </div>
  <div class="navigation">
    {$_log_pages_}
  </div>
  <script src='%PATH_PUBLIC%/js/core/jquery.infiniteScroll{$_compress_files_suffix_}.js' type='text/javascript'></script>
  <script type="text/javascript">
    $(document).ready(function(){
      $('#js-logs').infinitescroll({
        navSelector  : "div.navigation",
        nextSelector : "div.navigation a:first",
        itemSelector : "#js-logs table",
        loadingImg   : "%PATH_IMAGES%/loading.gif",
        loadingText  : '',
        donetext     : ''
      });
    });
  </script>
</section>