<form action='/gallery/{$_request_id_}/{$smarty.get.action}' method='post' enctype='multipart/form-data'>
  <h1>{if $smarty.get.action == 'createfile'}{$lang.gallery.files.title.create}{else}{$lang.gallery.files.title.update}{/if}</h1>
  {if $smarty.get.action == 'createfile'}
    <p {if isset($error.file)}class="error" title="{$error.file}"{/if}>
      <label for='input-file'>{$lang.gallery.files.label.choose} <span title="{$lang.global.required}">*</span></label>
      <input type='file' name='file[]' id="input-file" multiple required />
    </p>
    <p>
      <label for='input-cut'>{$lang.global.cut} <span title="{$lang.global.required}">*</span></label>
      <select name='cut' id="input-cut">
        <option value='c' {if $default == 'c'}default='default'{/if}>{$lang.gallery.files.label.cut}</option>
        <option value='r' {if $default == 'r'}default='default'{/if}>{$lang.gallery.files.label.resize}</option>
      </select>
    </p>
  {/if}
  <p>
    <label for='input-content'>{$lang.global.description}</label>
    <input type='text' name='content' id="input-content" value='{$content}' />
  </p>
  <p class="center">
    <input type='hidden' value='formdata' name='{$smarty.get.action}_gallery' />
    <input type='submit' value='{if $smarty.get.action == 'createfile'}{$lang.gallery.files.title.create}{else}{$lang.gallery.files.title.update}{/if}' />
    {if $smarty.get.action == 'updatefile'}
      <input type='hidden' value='{$_request_id_}' name='id' />
      <input type='button' value='{$lang.global.destroy.destroy}' onclick="candy.system.confirmDestroy('/gallery/{$_request_id_}/destroyfile?album_id={$album_id}')" />
      <input type='reset' value='{$lang.global.reset}' />
    {/if}
  </p>
</form>
<script type='text/javascript' src='%PATH_JS%/core/jquery.tiptip{$_compress_files_suffix_}.js'></script>
<script type="text/javascript">
  $('p.error').tipTip({ maxWidth: "auto" });
</script>