{if $USER_RIGHT >= 3}
  <p>
    <a href='/gallery/{$_request_id_}/createfile'>
      <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='' />
      {$lang_create_entry_headline}
    </a>
  </p>
{/if}
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
  <blockquote>{$gallery_description}</blockquote>
{/if}
{if !$files}
  <div class='error' id='js-error' onclick="hideDiv('js-error')">
    <p>{$lang_no_files_uploaded}</p>
  </div>
{else}
  <ul id="gallery_files" class="image-overlay">
    {foreach $files as $f}
      <li class="gallery_files">
        <div class="image">
          <a href='{$f.url_popup}' rel="images">
            <img src='{$f.url_thumb}'
                 alt='{$f.description}'
                 title='{$f.description}' />
            <div class="caption">
              <h3>{$f.description}</h3>
              <p>
                {$lang_uploaded_at}: {$f.date}
              </p>
            </div>
          </a>
          <a href="/gallery/{$f.id}/updatefile">
            <img src="%PATH_IMAGES%/spacer.png" class="icon-update" alt="{$lang_update}" />
          </a>
          <a href="/gallery/{$f.id}/destroyfile">
            <img src="%PATH_IMAGES%/spacer.png" class="icon-destroy" alt="{$lang_destroy}" />
          </a>
        </div>
      </li>
    {/foreach}
  </ul>
{/if}
<div class="navigation">
  {$_album_pages_}
</div>
<script language='javascript' src='%PATH_PUBLIC%/js/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script language='javascript' src='%PATH_PUBLIC%/js/core/jquery.ImageOverlay{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script language='javascript' src='%PATH_PUBLIC%/js/core/jquery.infiniteScroll{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script type="text/javascript">
  $(document).ready(function(){
    $('#gallery_files').infinitescroll({
      navSelector  : "div.navigation",
      nextSelector : "div.navigation a:first",
      itemSelector : ".gallery_files",
      loadingImg   : "%PATH_IMAGES%/loading.gif",
      loadingText  : '',
      donetext     : ''
    }, function() {
      $(".image a").fancybox();
      $('#gallery_files').ImageOverlay({
        overlay_speed: 'fast',
        overlay_speed_out: 'slow'
      });
    });

    $(".image a").fancybox();
    $('#gallery_files').ImageOverlay({
      overlay_speed: 'fast',
      overlay_speed_out: 'slow'
    });
  });
</script>