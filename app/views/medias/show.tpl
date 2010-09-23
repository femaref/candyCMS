{if $USER_RIGHT > 3}
  <p>
    <a href='/Media/create'>
      <img src='%PATH_IMAGES%/spacer.gif' class="icon-upload" alt='' />
      {$lang_file_create}
    </a>
  </p>
  <table>
    <tr>
      <th colspan='5'>{$lang_headline}</th>
    </tr>
    {if !$files}
      <tr>
        <td colspan="5">
          <div class='error nom' id='error' title='{$lang_no_files}' onclick="hideDiv('error')">
            <p>{$lang_no_files}</p>
          </div>
        </td>
      </tr>
    {else}
      {foreach from=$files item=f}
        <tr style='background:{cycle values="transparent,#eee"}'>
          <td style='width:10%'>
            {if ($f.type == 'jpg' || $f.type == 'jpeg' || $f.type == 'gif' || $f.type == 'png')}
              <img src='%PATH_UPLOAD%/temp/media/{$f.name}'
                   width='32' height='32' alt='{$f.type}' />
            {else}
              <img src='%PATH_IMAGES%/spacer.gif' class="filemanager-{$f.type}"
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
            <img src='%PATH_IMAGES%/spacer.gif' class="icon-destroy" alt='{$lang_destroy}'
                 title='{$lang_destroy}'
                 onclick="confirmDelete('/Media/destroy/{$f.name}')" />
          </td>
        </tr>
      {/foreach}
    {/if}
  </table>
  <script type="text/javascript">
    var sFilesSuffix = '{$_compress_files_suffix_}';
    {literal}
      window.addEvent('domready', function() {
        new Asset.javascript('%PATH_PUBLIC%/js/core/slimbox' + sFilesSuffix + '.js');
      });
    {/literal}
  </script>
{/if}
