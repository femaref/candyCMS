<form method='post' action='/gallery/{$smarty.get.action}'>
  <h1>{if $smarty.get.action == 'create'}{$lang.gallery.albums.title.create}{else}{$lang.gallery.albums.title.update|replace:'%p':$title}{/if}</h1>
  <p {if isset($error.title)}class="error" title="{$error.title}"{/if}>
    <label for='input-title'>{$lang.global.title}<span title="{$lang.global.required}">*</span></label>
    <input name='title' value='{$title}' id="input-title" type='text' autofocus required />
  </p>
  <p>
    <label for='input-content'>{$lang.global.description}</label>
    <input name='content' value='{$content}' id="input-content" type='text' />
  </p>
  <p class="center">
    <input type='submit' value='{if $smarty.get.action == 'create'}{$lang.global.create.create}{else}{$lang.global.update.update}{/if}' />
    <input type='hidden' value='formdata' name='{$smarty.get.action}_gallery' />
    {if $smarty.get.action == 'update'}
      <input type='hidden' value='{$_request_id_}' name='id' />
      <input type='button' value='{$lang.gallery.albums.title.destroy}'
        onclick="candy.system.confirmDestroy('/gallery/{$_request_id_}/destroy')" />
      <input type='reset' value='{$lang.global.reset}' />
    {/if}
  </p>
</form>
<script type='text/javascript' src='%PATH_JS%/core/jquery.tiptip{$_compress_files_suffix_}.js'></script>
<script type="text/javascript">
  $('p.error').tipTip({ maxWidth: "auto" });
</script>