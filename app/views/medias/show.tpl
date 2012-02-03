{if $USER_ROLE >= 3}
  <p class="center">
    <a href='/media/create'>
      <i class="icon-plus-sign"></i>
      {$lang.global.create.entry}
    </a>
  </p>

  <h1>{$lang.global.manager.media}</h1>
  {if !$files}
    <tr>
      <td colspan="5">
        <div class='error' title='{$lang.error.missing.files}'>
          <h4>{$lang.error.missing.files}</h4>
        </div>
      </td>
    </tr>
  {/if}
  <table class="sortTable tablesorter zebra-striped">
    <thead>
      <tr>
        <th></th>
        <th class="headerSortDown">{$lang.global.file}</th>
        <th>{$lang.global.size}</th>
        <th>{$lang.global.upload.at}</th>
        <th></th>
      </tr>
    <thead>
    <tbody>
      {foreach $files as $f}
        <tr class='{cycle values="row1,row2"}'>
          <td style='width:5%'>
            {if ($f.type == 'jpg' || $f.type == 'jpeg' || $f.type == 'gif' || $f.type == 'png')}
              <img src='%PATH_UPLOAD%/temp/media/{$f.name}'
                   width='32' height='32' alt='{$f.type}' />
            {else}
              <img src='%PATH_IMAGES%/files/{$f.type}.png'
                   width='32' height='32' alt='{$f.type}' />
            {/if}
          </td>
          <td style='width:60%' class="left">
            {if ($f.type == 'png' || $f.type == 'gif' || $f.type == 'jpg' || $f.type == 'jpeg')}
              <a href='%PATH_UPLOAD%/media/{$f.name}'
                 class="js-fancybox"
                 rel="image"
                 title='{$f.name} - ({$f.dim[0]} x {$f.dim[1]} px)'>
                {$f.name}
              </a> ({$f.dim[0]} x {$f.dim[1]} px)
            {else}
              <a href='%PATH_UPLOAD%/media/{$f.name}'>
                {$f.name}
              </a>
            {/if}
            <input type='text' class="copybox" value='%PATH_UPLOAD%/media/{$f.name}' onclick="this.focus();this.select();" />
          </td>
          <td style='width:15%'>
            {$f.size}
          </td>
          <td style='width:15%'>
            {$f.cdate}
          </td>
          <td style='width:5%'>
            <a href="#" onclick="candy.system.confirmDestroy('/media/{$f.name}/destroy')">
              <i class="icon-remove-sign"></i>
            </a>
          </td>
        </tr>
      {/foreach}
    </tbody>
  </table>
  <script src='%PATH_JS%/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
  <script type="text/javascript">
    $(document).ready(function(){
      $(".js-fancybox").fancybox({ nextEffect : 'fade', prevEffect : 'fade' });
    });
  </script>
{/if}