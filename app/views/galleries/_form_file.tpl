{strip}
  <form action='/{$_REQUEST.controller}/{$_REQUEST.id}/{$_REQUEST.action}' method='post'
        enctype='multipart/form-data' class='form-horizontal'>
    <div class='page-header'>
      <h1>
        {if $_REQUEST.action == 'createfile'}
          {$lang.galleries.files.title.create}
        {else}
          {$lang.galleries.files.title.update}
        {/if}
      </h1>
    </div>
    {if $_REQUEST.action == 'createfile'}
      <div class='control-group{if isset($error.file)} alert alert-error{/if}'>
        <label for='input-file' class='control-label'>
          {$lang.galleries.files.label.choose} <span title="{$lang.global.required}">*</span>
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
          <select name='cut' id="input-cut" class='span4 required'>
            <option value='c' {if $default == 'c'}default='default'{/if}>{$lang.galleries.files.label.cut}</option>
            <option value='r' {if $default == 'r'}default='default'{/if}>{$lang.galleries.files.label.resize}</option>
          </select>
        </div>
      </div>
    {/if}
    <div class='control-group'>
      <label for='input-content' class='control-label'>
        {$lang.global.description}
      </label>
      <div class='controls'>
        <input class='span4' type='text' name='content' id="input-content" value="{$content}" />
        <span class='help-inline'></span>
      </div>
    </div>
    <div class='form-actions'>
      <input type='submit' class='btn btn-primary'
            value="{if $_REQUEST.action == 'createfile'}{$lang.galleries.files.title.create}{else}{$lang.galleries.files.title.update}{/if}" />
      {if $_REQUEST.action == 'updatefile'}
        <input type='button' value='{$lang.global.destroy.destroy}' class='btn btn-danger'
        onclick="confirmDestroy('/{$_REQUEST.controller}/{$_REQUEST.id}/destroyfile?album_id={$album_id}')" />
        <input class='btn' type='reset' value='{$lang.global.reset}' />
        <input type='hidden' value='{$_REQUEST.id}' name='id' />
      {/if}
      <input type='hidden' value='formdata' name='{$_REQUEST.action}_{$_REQUEST.controller}' />
    </div>
  </form>
  <script type='text/javascript'>
    $('#input-content').bind('keyup', function() {
      countCharLength(this, 160);
    });
  </script>
{/strip}