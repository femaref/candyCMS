<form action='/gallery/{$_request_id_}/{$smarty.get.action}' method='post' enctype='multipart/form-data'>
  <h1>{$lang_headline}</h1>
  {if $smarty.get.action == 'createfile'}
    <p {if isset($error_file)}class="error" title="{$error_file}"{/if}>
      <label for='input-file'>{$lang_file_choose} *</label>
      <input type='file' name='file[]' id="input-file" multiple required />
    </p>
    <p>
      <label for='input-cut'>{$lang_cut} *</label>
      <select name='cut' id="input-cut">
        <option value='c' {if $default == 'c'}default='default'{/if}>{$lang_create_file_cut}</option>
        <option value='r' {if $default == 'r'}default='default'{/if}>{$lang_create_file_resize}</option>
      </select>
    </p>
  {/if}
  <p>
    <label for='input-content'>{$lang_description}</label>
    <input type='text' name='content' id="input-content" value='{$content}' />
  </p>
  <p class="center">
    <input type='hidden' value='formdata' name='{$smarty.get.action}_gallery' />
    <input type='submit' value='{$lang_headline}' />
    {if $smarty.get.action == 'updatefile'}
      <input type='hidden' value='{$_request_id_}' name='id' />
      <input type='button' value='{$lang_destroy}' onclick="confirmDelete('/gallery/{$_request_id_}/destroyfile')" />
      <input type='reset' value='{$lang_reset}' />
    {/if}
  </p>
</form>