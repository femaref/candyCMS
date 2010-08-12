{if $AJAX == false}
  <div class='gallery_files' id="js-ajax_reload" name="reload">
{/if}
{if $UR > 3}
  <p>
    <a href='/Gallery/createfile/{$id}'>
      <img src='%PATH_IMAGES%/spacer.gif' class="icon-create" alt='' />
      {$lang_create_entry_headline}
    </a>
  </p>
{/if}
<h2>
  {$gallery_name} ({$file_no} {$lang_files})
  {if $UR > 3}
    <a href='/Gallery/update/{$id}'>
      <img src='%PATH_IMAGES%/spacer.gif' class="icon-update" alt='{$lang_update}'
            title='{$lang_update}' />
    </a>
  {/if}
</h2>
{if !$files}
  <div class='error' id='error' title='{$lang_no_files_yet}' onclick="hideDiv('error')">
    <p>{$lang_no_files_yet}</p>
  </div>
{else}
  {if $gallery_description !== ''}
    <div class='quote'>{$gallery_description}</div>
  {/if}
  {$albumPages}
  {foreach from=$files item=f}
    <a href='{$f.full_path}/{$popup_path}/{$f.file}' rel='lightbox[]' title='
      {if $UR > 3}
        {$lang_uploaded_at}: {$f.date}
        <br />
        <a href="/Gallery/updatefile/{$f.id}">
            <img src="%PATH_IMAGES%/spacer.gif" class="icon-update" alt="{$lang_update}" />
        </a>
        &nbsp;
        <a href="/Gallery/destroyfile/{$f.id}">
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
           style="background: url('{$f.full_path}/{$f.dim}/{$f.file}') center no-repeat" />
    </a>
  {/foreach}
  <p>{$albumPages}</p>
{/if}
{if $AJAX == false}
  </div>
{/if}
{literal}
  <script type="text/javascript">
    window.addEvent('domready', function() {
      new Asset.javascript('%PATH_PUBLIC%/js/slimbox-min.js');
    });
  </script>
{/literal}