{strip}
  {if $USER_ROLE >= 3}
    <p class='center'>
      <a href='/media/create'>
        <img src='{$_PATH.images}/candy.global/spacer.png'
            class='icon-create'
            alt='{$lang.global.create.entry}'
            width='16' height='16' />
        {$lang.global.create.entry}
      </a>
    </p>
    <div class='page-header'>
      <h1>
        {$lang.global.manager.media}
      </h1>
    </div>
    {if !$files}
      <div class='alert alert-warning'>
        <h4>{$lang.error.missing.files}</h4>
      </div>
    {/if}
    <table class='table'>
      <thead>
        <tr>
          <th class='column-icon'></th>
          <th class='column-file headerSortDown'>{$lang.global.file}</th>
          <th class='column-size'>{$lang.global.size}</th>
          <th class='column-uploaded_at center'>{$lang.global.upload.at}</th>
          <th class='column-actions'></th>
        </tr>
      </thead>
      <tbody>
        {foreach $files as $f}
          <tr>
            <td class='center'>
              {if ($f.type == 'jpg' || $f.type == 'jpeg' || $f.type == 'gif' || $f.type == 'png')}
                <img src='{$_PATH.upload}/temp/media/{$f.name}' class='thumbnail'
                    width='32' height='32' alt='{$f.type}' />
              {else}
                <img src='{$_PATH.images}/files/{$f.type}.png' class='thumbnail'
                    width='32' height='32' alt='{$f.type}' />
              {/if}
            </td>
            <td>
              {if ($f.type == 'png' || $f.type == 'gif' || $f.type == 'jpg' || $f.type == 'jpeg')}
                <a href='{$_PATH.upload}/media/{$f.name}'
                  class='js-fancybox'
                  rel='image'
                  title='{$f.name} - ({$f.dim[0]} x {$f.dim[1]} px)'>
                  {$f.name}
                </a> ({$f.dim[0]} x {$f.dim[1]} px)
              {else}
                <a href='{$_PATH.upload}/media/{$f.name}'>
                  {$f.name}
                </a>
              {/if}
              <input type='text' class='copybox' value='{$_PATH.upload}/media/{$f.name}'
                    onclick='this.focus();this.select();' />
            </td>
            <td>
              {$f.size}
            </td>
            <td class='center'>
              {$f.cdate}
            </td>
            <td>
              <a href="#" onclick="confirmDestroy('/media/{$f.name}/destroy')">
                <img src='{$_PATH.images}/candy.global/spacer.png'
                    class='icon-destroy js-tooltip'
                    alt='{$lang.global.destroy.destroy}'
                    title='{$lang.global.destroy.destroy}'
                    width='16' height='16' />
              </a>
            </td>
          </tr>
        {/foreach}
      </tbody>
    </table>
    <script src='{$_PATH.js}/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
    <script type='text/javascript' src='{$_PATH.js}/core/jquery.tablesorter{$_compress_files_suffix_}.js'></script>
    <script type="text/javascript">
      $(document).ready(function(){
        $(".js-fancybox").fancybox({ nextEffect : 'fade', prevEffect : 'fade' });
      });

      $('table').tablesorter();
    </script>
  {/if}
{/strip}