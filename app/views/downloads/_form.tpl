{strip}
  <div class='page-header'>
    <h1>{$lang.global.download}</h1>
  </div>
  <form action='/{$_REQUEST.controller}/{$_REQUEST.action}' method='post'
        enctype='multipart/form-data' class='form-horizontal'>
    {if $_REQUEST.action == 'create'}
      <div class='control-group{if isset($error.file)} alert alert-error{/if}'>
        <label for='input-file' class='control-label'>
          {$lang.download.label.choose} <span title="{$lang.global.required}">*</span>
        </label>
        <div class='controls'>
          <input class='input-file span4 required' type='file' name='file[]'
                id="input-file" required />
        </div>
      </div>
    {/if}
    <div class='control-group{if isset($error.title)} alert alert-error{/if}'>
      <label for='input-title' class='control-label'>
        {$lang.global.title} <span title="{$lang.global.required}">*</span>
      </label>
      <div class='controls'>
        <input class='span4 required' type='text' name='title' id="input-title"
              value="{$title}" required />
        <span class='help-inline'>
          {if isset($error.title)}
            {$error.title}
          {/if}
        </span>
      </div>
    </div>
    <div class='control-group{if isset($error.category)} alert alert-error{/if}'>
      <label for='input-category' class='control-label'>
        {$lang.global.category} <span title="{$lang.global.required}">*</span>
      </label>
      <div class='controls'>
        <input type='text' name='category' id="input-category" placeholder=""
              data-provide="typeahead" value="{$category}"
              data-source='{$_categories_}' data-items="8"
              class='span4 required' autocomplete="off" required />
        {if isset($error.category)}<span class='help-inline'>{$error.category}</span>{/if}
      </div>
    </div>
    <div class='control-group{if isset($error.content)} alert alert-error{/if}'>
      <label for='input-content' class='control-label'>
        {$lang.global.description}
      </label>
      <div class='controls'>
        <input class='span4' type='text' name='content' id="input-content"
              value="{$content}" />
        {if isset($error.content)}<span class='help-inline'>{$error.content}</span>{/if}
      </div>
    </div>
    {if $_REQUEST.action == 'update'}
      <div class='control-group'>
        <label for='input-downloads' class='control-label'>
          {$lang.global.downloads}
        </label>
        <div class='controls'>
          <input class='span4 required' type='text' name='downloads'
                id="input-downloads" value='{$downloads}' />
        </div>
      </div>
    {/if}
    <div class="form-actions">
      <input type='hidden' value='formdata' name='{$_REQUEST.action}_{$_REQUEST.controller}' />
      <input type='submit' class='btn btn-primary'
            value='{if $_REQUEST.action == 'create'}{$lang.global.create.create}{else}{$lang.global.update.update}{/if}' />
      {if $_REQUEST.action == 'update'}
        <input type='button' class='btn btn-danger'
              value='{$lang.global.destroy.destroy}'
              onclick="confirmDestroy('/{$_REQUEST.controller}/{$_REQUEST.id}/destroy')" />
        <input type='hidden' value='{$_REQUEST.id}' name='id' />
        <input type='reset' class='btn' value='{$lang.global.reset}' />
      {/if}
    </p>
  </form>
  <script type='text/javascript' src='{$_PATH.js}/core/jquery.typeahead{$_SYSTEM.compress_files_suffix}.js'></script>
  <script type='text/javascript'>
    $('#input-title').bind('keyup', function() {
      countCharLength(this, 128);
    });
  </script>
{/strip}