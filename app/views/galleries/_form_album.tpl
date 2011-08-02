<form method='post' action='{$_action_url_}'>
  <h1>{$lang_headline}</h1>
  <p {if isset($error_title)}class="error" title="{$error_title}"{/if}>
    <label for='input-title'>{$lang_title} <span title="{$lang_required}">*</span></label>
    <input name='title' value='{$title}' id="input-title" type='text' autofocus required />
  </p>
  <p>
    <label for='input-description'>{$lang_description}</label>
    <input name='content' value='{$content}' id="input-description" type='text' />
  </p>
  <p class="center">
    <input type='submit' value='{$lang_submit}' />
    <input type='hidden' value='{$_request_id_}' name='id' />
    <input type='hidden' value='formdata' name='{$_formdata_}' />
    {if $smarty.get.action == 'update'}
      <input type='reset' value='{$lang_reset}' />
      <input type='button' value='{$lang_destroy_entry}'
        onclick="confirmDelete('/gallery/{$_request_id_}/destroy')" />
    {/if}
  </p>
</form>