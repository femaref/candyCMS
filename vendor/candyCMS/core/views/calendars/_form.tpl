{strip}
  <div class='page-header'>
    <h1>{$lang.global.calendar}</h1>
  </div>
  <form method='post' class='form-horizontal'
        action='/{$_REQUEST.controller}/{if isset($_REQUEST.id)}{$_REQUEST.id}/{/if}{$_REQUEST.action}'>
     <div class='control-group{if isset($error.title)} alert alert-error{/if}'>
      <label for='input-title'>
        {$lang.global.title} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        <input class='span4 required focused' type='text' name='title' id='input-title'
              value="{$title}" required autofocus />
        <span class='help-inline'>
          {if isset($error.title)}
            {$error.title}
          {/if}
        </span>
      </div>
    </div>
    <div class='control-group{if isset($error.content)} alert alert-error{/if}'>
      <label for='input-content' class='control-label'>
        {$lang.global.description}
      </label>
      <div class='controls'>
        <input class='span4' type='text' name='content' id='input-content' value='{$content}' />
      </div>
    </div>
    <div class='control-group{if isset($error.start_date)} alert alert-error{/if}'>
      <label for='input-start_date' class='control-label'>
        {$lang.global.date.start} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        <input type='date' name='start_date' id='input-start_date' value="{$start_date}"
              min='{$_SYSTEM.date}' class='span4 required' autocomplete required />
        {if isset($error.start_date)}
          <span class='help-inline'>{$error.start_date}</span>
        {/if}
        <p class='help-block'>
          {$lang.calendars.info.date_format}
        </p>
      </div>
    </div>
    <div class='control-group{if isset($error.end_date)} alert alert-error{/if}'>
      <label for='input-end_date' class='control-label'>
        {$lang.global.date.end}
      </label>
      <div class='controls'>
        <input type='date' name='end_date' id='input-end_date' value="{$end_date}"
              class='span4' min='{$_SYSTEM.date}' autocomplete />
        {if isset($error.end_date)}
          <span class='help-inline'>{$error.end_date}</span>
        {/if}
        <p class='help-block'>
          {$lang.calendars.info.date_format}
        </p>
      </div>
    </div>
    <div data-role='fieldcontain' class='form-actions'>
      <input class='btn btn-primary' type='submit' data-theme='b'
             value="{if $_REQUEST.action == 'create'}{$lang.global.create.create}{else}{$lang.global.update.update}{/if}" />
      {if $_REQUEST.action == 'update'}
        <input class='btn btn-danger' type='button' value='{$lang.global.destroy.destroy}'
              onclick="confirmDestroy('/{$_REQUEST.controller}/{$_REQUEST.id}/destroy')" />
        <input class='btn' type='reset' value='{$lang.global.reset}' />
        <input type='hidden' value="{$_REQUEST.id}" name='id' />
      {/if}
      <input type='hidden' value='formdata' name='{$_REQUEST.action}_{$_REQUEST.controller}' />
    </div>
  </form>
  <script type='text/javascript'>
    $('#input-title').bind('keyup', function() {
      countCharLength(this, 128);
    });
  </script>
{/strip}