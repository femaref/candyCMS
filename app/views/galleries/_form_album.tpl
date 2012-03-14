{strip}
  <form method='post' action='/{$_REQUEST.controller}/{$_REQUEST.action}' class='form-horizontal'>
    <div class='page-header'>
      <h1>
        {if $_REQUEST.action == 'create'}
          {$lang.gallery.albums.title.create}
        {else}
          {$lang.gallery.albums.title.update|replace:'%p':$title}
        {/if}
      </h1>
    </div>
    <div class='control-group{if isset($error.title)} alert alert-error{/if}'>
      <label for='input-title' class='control-label'>
        {$lang.global.title} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        <input name='title' value="{$title}" id='input-title'
               class='required span4 focused'
               type='text' autofocus required />
        <span class='help-inline'>
          {if isset($error.title)}
            {$error.title}
          {/if}
        </span>
      </div>
    </div>
    <div class='control-group'>
      <label for='input-content' class='control-label'>
        {$lang.global.description}
      </label>
      <div class='controls'>
        <input name='content' value="{$content}" id='input-content' type='text' class='span4' />
        <span class='help-inline'></span>
      </div>
    </div>
    <div class='form-actions'>
      <input type='submit' class='btn btn-primary'
            value="{if $_REQUEST.action == 'create'}{$lang.global.create.create}{else}{$lang.global.update.update}{/if}" />
      <input type='hidden' value='formdata' name='{$_REQUEST.action}_{$_REQUEST.controller}' />
      {if $_REQUEST.action == 'update'}
        <input type='hidden' value='{$_REQUEST.id}' name='id' />
        <input type='button' value='{$lang.gallery.albums.title.destroy}' class='btn btn-danger'
          onclick="confirmDestroy('/{$_REQUEST.controller}/{$_REQUEST.id}/destroy')" />
        <input type='reset' value='{$lang.global.reset}' class='btn' />
      {/if}
    </div>
  </form>
  <script type='text/javascript'>
    $('#input-title').bind('keyup', function() {
      countCharLength(this, 50);
    });
    $('#input-content').bind('keyup', function() {
      countCharLength(this, 160);
    });
  </script>
{/strip}