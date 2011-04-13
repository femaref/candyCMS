{if $USER_RIGHT >= 3}
  <p>
    <a href='/media/create'>
      <img src='%PATH_IMAGES%/spacer.png' class="icon-upload" alt='' />
      {$lang_file_create}
    </a>
  </p>
  <table>
    <tr>
      <th colspan='5'>
        <h1>{$lang_headline}</h1>
      </th>
    </tr>
    {if !$files}
      <tr>
        <td colspan="5">
          <div class='error nom' id='js-error' title='{$lang_no_files}'>
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
              <a href='%PATH_UPLOAD%/media/{$f.name}' rel='lightbox[]' title='{$f.name} - ({$f.dim[0]} x {$f.dim[1]} px)'>
                {$f.name}
              </a> ({$f.dim[0]} x {$f.dim[1]} px)
            {else}
              <a href='%PATH_UPLOAD%/media/{$f.name}'>
                {$f.name}
              </a>
            {/if}
            <br />
            <input type='text' value='%PATH_UPLOAD%/media/{$f.name}' class='inputsmall'
                   onclick="this.focus();this.select();" />
          </td>
          <td style='20%'>
            {$f.size}
          </td>
          <td style='width:20%'>
            {$f.cdate}
          </td>
          <td style='width:5%'>
            <img src='%PATH_IMAGES%/spacer.png' class="icon-destroy" alt='{$lang_destroy}'
                 title='{$lang_destroy}'
                 onclick="confirmDelete('/media/destroy/{$f.name}')" />
          </td>
        </tr>
      {/foreach}
    {/if}
  </table>
  <script type="text/javascript">
    // TODO: Load js after
    //window.addEvent('domready', function() {
    //  new Asset.javascript('%PATH_PUBLIC%/js/core/slimbox{$_compress_files_suffix_}.js');
    //});
  </script>
{/if}
