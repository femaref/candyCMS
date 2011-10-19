<form method='post' action='/gallery/{$smarty.get.action}'>
  <h1>{if $smarty.get.action == 'create'}{$lang.gallery.albums.title.create}{else}{$lang.gallery.albums.title.update|replace:'%p':$title}{/if}</h1>
  <p {if isset($error_title)}class="error" title="{$error_title}"{/if}>
    <label for='input-title'>{$lang.global.title} <span title="{$lang.global.required}">*</span></label>
    <input name='title' value='{$title}' id="input-title" type='text' autofocus required />
  </p>
  <p>
    <label for='input-description'>{$lang.global.description}</label>
    <input name='content' value='{$content}' id="input-description" type='text' />
  </p>
  <p class="center">
    <input type='submit' value='{$lang.gallery.albums.title.create}' />
    <input type='hidden' value='formdata' name='{$smarty.get.action}_gallery' />
    {if $smarty.get.action == 'update'}
      <input type='hidden' value='{$_request_id_}' name='id' />
      <input type='button' value='{$lang.gallery.albums.title.destroy}'
        onclick="confirmDelete('/gallery/{$_request_id_}/destroy')" />
      <input type='reset' value='{$lang.global.reset}' />
    {/if}
  </p>
</form>