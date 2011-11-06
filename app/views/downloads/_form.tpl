<form action='/download/{$smarty.get.action}' method='post' enctype='multipart/form-data'>
  <h1>{$lang.global.download}</h1>
  {if $smarty.get.action == 'create'}
    <p {if isset($error.file)}class="error" title="{$error.file}"{/if}>
      <label for='input-file'>{$lang.download.label.choose}<span title="{$lang.global.required}">*</span></label>
      <input type='file' name='file[]' id="input-file" required />
    </p>
  {/if}
  <p {if isset($error.title)}class="error" title="{$error.title}"{/if}>
    <label for='input-title'>{$lang.global.title}<span title="{$lang.global.required}">*</span></label>
    <input type='text' name='title' id="input-title" value='{$title}' required />
  </p>
  <p {if isset($error.category)}class="error" title="{$error.category}"{/if}>
    <label for='input-category'>{$lang.global.category}<span title="{$lang.global.required}">*</span></label>
    <input type='text' name='category' id="input-category" placeholder="" value='{$category}' required />
  </p>
  <p {if isset($error.content)}class="error" title="{$error.content}"{/if}>
    <label for='input-content'>{$lang.global.description}</label>
    <input type='text' name='content' id="input-content" value='{$content}' />
  </p>
  {if $smarty.get.action == 'update'}
    <p>
      <label for='input-downloads'>{$lang.global.downloads}</label>
      <input type='text' name='downloads' id="input-downloads" value='{$downloads}' />
    </p>
  {/if}
  <p class="center">
    <input type='hidden' value='formdata' name='{$smarty.get.action}_download' />
    <input type='submit' value='{$lang.download.title.create}' />
    {if $smarty.get.action == 'update'}
      <input type='button' value='{$lang.global.destroy.destroy}' onclick="candy.system.confirmDestroy('/download/{$_request_id_}/destroy')" />
      <input type='hidden' value='{$_request_id_}' name='id' />
      <input type='reset' value='{$lang.global.reset}' />
    {/if}
  </p>
</form>