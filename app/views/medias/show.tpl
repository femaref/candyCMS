<section id="media">
  {if $USER_RIGHT >= 3}
    <p class="center">
      <a href='/media/create'>
        <img src='%PATH_IMAGES%/spacer.png' class="icon-upload" alt='' />
        {$lang_file_create}
      </a>
    </p>
    <h1>{$lang_headline}</h1>
    <table>
      {if !$files}
        <tr>
          <td colspan="5">
            <div class='error' id='js-error' title='{$lang_no_files}'>
              <p>{$lang_no_files}</p>
            </div>
          </td>
        </tr>
      {else}
        {foreach $files as $f}
          <tr class='{cycle values="row1,row2"}'>
            <td style='width:10%'>
              {if ($f.type == 'jpg' || $f.type == 'jpeg' || $f.type == 'gif' || $f.type == 'png')}
                <img src='%PATH_UPLOAD%/temp/media/{$f.name}'
                     width='32' height='32' alt='{$f.type}' />
              {else}
                <img src='%PATH_IMAGES%/spacer.png' class="filemanager-{$f.type}"
                     width='32' height='32' alt='{$f.type}' />
              {/if}
            </td>
            <td style='text-align:left;width:45%'>
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
            <td style='width:20%'>
              {$f.size}
            </td>
            <td style='width:20%'>
              {$f.cdate}
            </td>
            <td style='width:5%'>
              <img src='%PATH_IMAGES%/spacer.png' class="icon-destroy pointer" alt='{$lang_destroy}'
                   title='{$lang_destroy}'
                   onclick="confirmDelete('/media/{$f.name}/destroy')" />
            </td>
          </tr>
        {/foreach}
      {/if}
    </table>
    <script src='%PATH_PUBLIC%/js/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
    <script type="text/javascript">
      $(document).ready(function(){
        $(".js-fancybox").fancybox();
      });
    </script>
  {/if}
</section>