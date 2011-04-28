{if $USER_RIGHT >= 3}
  <p class="center">
    <a href='/gallery/{$_request_id_}/createfile'>
      <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='' />
      {$lang_create_entry_headline}
    </a>
  </p>
{/if}
<section>
  <h1>
    {$gallery_name} ({$file_no} {$lang_files})
    <a href='/rss/gallery/{$_request_id_}'>
      <img src='%PATH_IMAGES%/spacer.png' class="icon-rss" alt='{$lang_rss_feed}' />
    </a>
    {if $USER_RIGHT >= 3}
      <a href='/gallery/{$_request_id_}/update'>
        <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang_update}'
              title='{$lang_update}' />
      </a>
    {/if}
  </h1>
  {if $gallery_description}
    <h3>{$gallery_description}</h3>
  {/if}
  {if !$files}
    <div class='error' id='js-error'>
      <p>{$lang_no_files_uploaded}</p>
    </div>
  {else}
    <ul class="js-caption">
      {foreach $files as $f}
        <li>
          <a href='{$f.url_popup}' class="js-fancybox" rel="images">
            <img src='{$f.url_thumb}'
                 alt='{$f.description}'
                 title='{$f.description}'
                 class="js-image"
                 rel="js-captify-{$f.id}" />
          </a>
          <div id="js-captify-{$f.id}">
            <h3>{$f.date}</h3>
            {if $USER_RIGHT >= 3}
              <a href="#" onclick="window.location='/gallery/{$f.id}/updatefile';return false">
                <img src="%PATH_IMAGES%/spacer.png" class="icon-update" alt="{$lang_update}" />
              </a>
              <a href="#" onclick="confirmDelete('/gallery/{$f.id}/destroyfile');return false">
                <img src="%PATH_IMAGES%/spacer.png" class="icon-destroy" alt="{$lang_destroy}" />
              </a>
            {/if}
          </div>
        </li>
      {/foreach}
    </ul>
  {/if}
</section>
<script language='javascript' src='%PATH_PUBLIC%/js/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script language='javascript' src='%PATH_PUBLIC%/js/core/jquery.captify{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script language='javascript' src='%PATH_PUBLIC%/js/core/jquery.lazyload{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script language='javascript' type="text/javascript">
  $(document).ready(function(){
    $(".js-fancybox").fancybox();
    $('img.js-image').captify({
      className: 'js-caption_bottom',
      opacity: '0.75',
      hideDelay: 0 });
    $(".js-image").lazyload();
  });
</script>