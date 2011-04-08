{if $_plugin_lazyload_}
  {$_plugin_lazyload_}
{/if}
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
  <div class='gallery_files'>
    {foreach $files as $f}
      <div class="image">
        <a href='{$f.url_popup}' rel="images" title='
          {if $USER_RIGHT >= 3}
            {$lang_uploaded_at}: {$f.date}
            <br />
            <a href="/gallery/{$f.id}/updatefile">
                <img src="%PATH_IMAGES%/spacer.png" class="icon-update" alt="{$lang_update}" />
            </a>
            <a href="/gallery/{$f.id}/destroyfile">
                <img src="%PATH_IMAGES%/spacer.png" class="icon-destroy" alt="{$lang_destroy}" />
            </a>
            <br />
            <input type="text" value="{$f.url_popup}" class="inputsmall"
                onclick="this.focus();this.select();" />
          {else}
            {$f.description}
          {/if}'>
          <img src='{$f.url_thumb}'
               alt='{$f.description}' id='{$f.loop}{$f.file}'
               title='{$f.description}' />
        </a>
      </div>
    {/foreach}
  </div>
{/if}
<div class="navigation">
  {$_album_pages_}
</div>
<script type="text/javascript">
  $(document).ready(function(){
    $('.gallery_files').infinitescroll({
      navSelector  : "div.navigation",
      nextSelector : "div.navigation a:first",
      itemSelector : ".gallery_files",
      loadingImg   : "%PATH_IMAGES%/loading.gif",
      loadingText  : '',
      donetext     : ''
    }, function() {
      $(".image a").fancybox();
    });

    $(".image a").fancybox();
  });
</script>