{if $_plugin_lazyload_}
  {$_plugin_lazyload_}
{/if}
{if $AJAX_REQUEST == false}
  <div class='gallery_files' id="js-ajax_reload" name="reload">
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
  {if $USER_RIGHT >= 3}
    <a href='/gallery/{$_request_id_}/update'>
      <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang_update}'
            title='{$lang_update}' />
    </a>
  {/if}
</h1>
{if !$files}
  <div class='error' id='js-error' onclick="hideDiv('js-error')">
    <p>{$lang_no_files_uploaded}</p>
  </div>
{else}
  {if $gallery_description}
    <blockquote>{$gallery_description}</blockquote>
  {/if}
  {$_album_pages_}
  {foreach $files as $f}
    <div class="image">
      <a href='{$f.url_popup}' rel='lightbox[]' title='
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
  <p>{$_album_pages_}</p>
  <a href='/rss/gallery/{$_request_id_}'>
    <img src='%PATH_IMAGES%/spacer.png' class="icon-rss" alt='{$lang_rss_feed}' />
  </a>
{/if}
{if $AJAX_REQUEST == false}
  </div>
{/if}
<script type="text/javascript">
  window.addEvent('domready', function() {
    new Asset.javascript('%PATH_PUBLIC%/js/core/slimbox{$_compress_files_suffix_}.js');
  });
</script>