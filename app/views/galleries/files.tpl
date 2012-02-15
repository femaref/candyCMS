{strip}
  {if $USER_ROLE >= 3}
    <p class="center">
      <a href='/gallery/{$_request_id_}/createfile'>
        <img src='%PATH_IMAGES%/spacer.png'
            class='icon-create'
            alt='{$lang.global.create.entry}'
            width='16' height='16' />
        {$lang.gallery.files.title.create}
      </a>
    </p>
  {/if}
  {if !$gallery_name}
    <div class='alert alert-warning'>
      <h4>{$lang.error.missing.entry}</h4>
    </div>
  {else}
    <header class="page-header">
      <h1>
        {$gallery_name}
        <small>
          ({$file_no} {$lang.global.files})
        </small>
        {if $USER_ROLE >= 3}
          <a href='/gallery/{$_request_id_}/update'>
            <img src='%PATH_IMAGES%/spacer.png'
                class='icon-update js-tooltip'
                alt='{$lang.global.update.update}'
                title='{$lang.global.update.update}'
                width='16' height='16' />
          </a>
        {/if}
      </h1>
    </header>
    {if $gallery_content}
      <p>{$gallery_content}</p>
    {/if}
    {if !$files}
      <div class='alert alert-warning'>
        <h4>{$lang.error.missing.files}</h4>
      </div>
    {else}
      <ul class="thumbnails">
        {foreach $files as $f}
          <li>
            <a href='{$f.url_popup}' class=" thumbnail js-fancybox js-tooltip"
              rel="images" title='{$f.content}'>
              <img src='{$f.url_thumb}' alt='{$f.content}' title='' class="js-image" />
            </a>
            {if $USER_ROLE >= 3}
              <p class='center'>
                <a href="/gallery/{$f.id}/updatefile">
                  <img src='%PATH_IMAGES%/spacer.png'
                      class='icon-update js-tooltip'
                      alt='{$lang.global.update.update}'
                      title='{$lang.global.update.update}'
                      width='16' height='16' />
                </a>
                <a href="#" onclick="confirmDestroy('/gallery/{$f.id}/destroyfile?album_id={$_request_id_}')">
                  <img src='%PATH_IMAGES%/spacer.png'
                      class='icon-destroy js-tooltip'
                      alt='{$lang.global.destroy.destroy}'
                      title='{$lang.global.destroy.destroy}'
                      width='16' height='16' />
                </a>
              </p>
            {/if}
          </li>
        {/foreach}
      </ul>
    <p class='center'>
      <a href='/rss/gallery/{$_request_id_}'>
        <img src='%PATH_IMAGES%/spacer.png' class="icon-rss" alt='{$lang.global.rss}' width="16" height="16" />
      </a>
    </p>
    {/if}
  {/if}
  <script src='%PATH_JS%/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
  <script src='%PATH_JS%/core/jquery.lazyload{$_compress_files_suffix_}.js' type='text/javascript'></script>
  <script type="text/javascript">
    $(document).ready(function(){
      $(".js-fancybox").fancybox({ nextEffect : 'fade', prevEffect : 'fade' });
      $(".js-image").lazyload({ threshold : 200, effect : "fadeIn" });
    });
  </script>
{/strip}