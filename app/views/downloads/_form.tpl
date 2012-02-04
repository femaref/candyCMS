<h1>{$lang.global.download}</h1>
<form action='/download/{$smarty.get.action}' method='post' enctype='multipart/form-data'>
  <fieldset>
  {if $smarty.get.action == 'create'}
    <div class='clearfix{if isset($error.file)} error{/if}'>
      <label for='input-file'>{$lang.download.label.choose} <span title="{$lang.global.required}">*</span></label>
      <div class='input'>
        <input type='file' name='file[]' id="input-file" required />
      </div>
    </div>
  {/if}
  <div class='clearfix{if isset($error.title)} error{/if}'>
    <label for='input-title'>{$lang.global.title} <span title="{$lang.global.required}">*</span></label>
    <div class='input'>
      <input type='text' name='title' id="input-title" value='{$title}' required />
    </div>
  </div>
  <div class='clearfix{if isset($error.category)} error{/if}'>
    <label for='input-category'>{$lang.global.category} <span title="{$lang.global.required}">*</span></label>
    <div class='input'>
      <input type='text' name='category' id="input-category" placeholder="" value='{$category}' required />
    </div>
  </div>
  <div class='clearfix{if isset($error.content)} error{/if}'>
    <label for='input-content'>{$lang.global.description}</label>
    <div class='input'>
      <input type='text' name='content' id="input-content" value='{$content}' />
    </div>
  </div>
  {if $smarty.get.action == 'update'}
    <div class='clearfix'>
      <label for='input-downloads'>{$lang.global.downloads}</label>
      <div class='input'>
        <input type='text' name='downloads' id="input-downloads" value='{$downloads}' />
      </div>
    </div>
  {/if}
  <div class="actions">
    <input type='hidden' value='formdata' name='{$smarty.get.action}_download' />
    <input type='submit' class='btn primary' value='{if $smarty.get.action == 'create'}{$lang.global.create.create}{else}{$lang.global.update.update}{/if}' />
    {if $smarty.get.action == 'update'}
      <input type='button' class='btn' value='{$lang.global.destroy.destroy}' onclick="candy.system.confirmDestroy('/download/{$_request_id_}/destroy')" />
      <input type='hidden' value='{$_request_id_}' name='id' />
      <input type='reset' class='btn' value='{$lang.global.reset}' />
    {/if}
  </p>
  </fieldset>
</form>