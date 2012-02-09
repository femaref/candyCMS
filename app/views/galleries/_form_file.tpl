<form action='/gallery/{$_request_id_}/{$smarty.get.action}' method='post'
      enctype='multipart/form-data' class='form-horizontal'>
  <div class='page-header'>
    <h1>
      {if $smarty.get.action == 'createfile'}
        {$lang.gallery.files.title.create}
      {else}
        {$lang.gallery.files.title.update}
      {/if}
    </h1>
  </div>
  {if $smarty.get.action == 'createfile'}
    <div class='control-group{if isset($error.file)} error{/if}'>
      <label for='input-file' class='control-label'>
        {$lang.gallery.files.label.choose} <span title="{$lang.global.required}">*</span>
      </label>
      <div class='controls'>
        <input class='span4 required' type='file' name='file[]'
               id="input-file" multiple required />
        {if isset($error.file)}<span class="help-inline">{$error.file}</span>{/if}
      </div>
    </div>
    <div class='control-group'>
      <label for='input-cut' class='control-label'>
        {$lang.global.cut} <span title="{$lang.global.required}">*</span>
      </label>
      <div class='controls'>
        <select name='cut' id="input-cut" class='span4'>
          <option value='c' {if $default == 'c'}default='default'{/if}>{$lang.gallery.files.label.cut}</option>
          <option value='r' {if $default == 'r'}default='default'{/if}>{$lang.gallery.files.label.resize}</option>
        </select>
      </div>
    </div>
  {/if}
  <div class='control-group'>
    <label for='input-content' class='control-label'>
      {$lang.global.description}
    </label>
    <div class='controls'>
      <input class='span4' type='text' name='content' id="input-content" value='{$content}' />
    </div>
  </div>
  <div class='form-actions'>
    <input type='submit' class='btn btn-primary'
           value="{if $smarty.get.action == 'createfile'}{$lang.gallery.files.title.create}{else}{$lang.gallery.files.title.update}{/if}" />
    {if $smarty.get.action == 'updatefile'}
      <input type='button' value='{$lang.global.destroy.destroy}' class='btn btn-danger'
      onclick="candy.system.confirmDestroy('/gallery/{$_request_id_}/destroyfile?album_id={$album_id}')" />
      <input class='btn' type='reset' value='{$lang.global.reset}' />
      <input type='hidden' value='{$_request_id_}' name='id' />
    {/if}
    <input type='hidden' value='formdata' name='{$smarty.get.action}_gallery' />
  </div>
</form>