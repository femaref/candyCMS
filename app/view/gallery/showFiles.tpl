{if $AJAX_REQUEST == false}
  <div class='gallery_files' id="js-ajax_reload" name="reload">
{/if}
{if $USER_RIGHT > 3}
  <p>
    <a href='/Gallery/{$id}/createfile'>
      <img src='%PATH_IMAGES%/spacer.gif' class="icon-create" alt='' />
      {$lang_create_entry_headline}
    </a>
  </p>
{/if}
<h2>
  {$gallery_name} ({$file_no} {$lang_files})
  {if $USER_RIGHT > 3}
    <a href='/Gallery/{$id}/update'>
      <img src='%PATH_IMAGES%/spacer.gif' class="icon-update" alt='{$lang_update}'
            title='{$lang_update}' />
    </a>
  {/if}
</h2>
{if !$files}
  <div class='error' id='error' onclick="hideDiv('error')">
    <p>{$lang_no_files_uploaded}</p>
  </div>
{else}
  {if $gallery_description}
    <div class='quote'>{$gallery_description}</div>
  {/if}
  {$_album_pages_}
  {foreach from=$files item=f}
    <a href='{$f.full_path}/{$popup_path}/{$f.file}' rel='lightbox[]' title='
      {if $USER_RIGHT > 3}
        {$lang_uploaded_at}: {$f.date}
        <br />
        <a href="/Gallery/{$f.id}/updatefile">
            <img src="%PATH_IMAGES%/spacer.gif" class="icon-update" alt="{$lang_update}" />
        </a>
        <a href="/Gallery/{$f.id}/destroyfile">
            <img src="%PATH_IMAGES%/spacer.gif" class="icon-destroy" alt="{$lang_destroy}" />
        </a>
        <br />
        <input type="text" value="{$f.full_path}/{$popup_path}/{$f.file}" class="inputsmall"
            onclick="this.focus();this.select();" />
      {else}
        {$f.description}
      {/if}'>
      <img src='%PATH_IMAGES%/spacer.gif' class='image' width='{$f.dim}'
           height='{$f.dim}' alt='{$f.description}' id='{$f.loop}{$f.file}'
           title='{$f.description}'
           style="background:#EFEFEF url('{$f.full_path}/{$f.dim}/{$f.file}') center no-repeat" />
    </a>
  {/foreach}
  <p>{$_album_pages_}</p>
{/if}
{if $AJAX_REQUEST == false}
  </div>
{/if}
<script type="text/javascript">
  var sFilesSuffix = '{$_compress_files_suffix_}';
  {literal}
    window.addEvent('domready', function() {
      new Asset.javascript('%PATH_PUBLIC%/js/slimbox' + sFilesSuffix + '.js');
    });
  {/literal}
</script>
