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
                 class="js-image" />
          </a>
          {if $USER_RIGHT >= 3}
            <div>
              <a href="/gallery/{$f.id}/updatefile">
                <img src="%PATH_IMAGES%/spacer.png" class="icon-update" alt="{$lang_update}" />
              </a>
              <a href="/gallery/{$f.id}/destroyfile">
                <img src="%PATH_IMAGES%/spacer.png" class="icon-destroy" alt="{$lang_destroy}" />
              </a>
            </div>
          {/if}
        </li>
      {/foreach}
    </ul>
  {/if}
</section>
<script src='%PATH_PUBLIC%/js/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script src='%PATH_PUBLIC%/js/core/jquery.lazyload{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script type="text/javascript">
  $(document).ready(function(){
    $(".js-fancybox").fancybox();
    $(".js-image").lazyload({ threshold : 200 });
  });
</script>