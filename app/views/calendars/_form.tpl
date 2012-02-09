<div class='page-header'>
  <h1>{$lang.global.calendar}</h1>
</div>
<form action='/calendar/{$smarty.get.action}' method='post' class='form-horizontal'>
  <div class='control-group{if isset($error.title)} error{/if}'>
    <label for='input-title'>
      {$lang.global.title} <span title="{$lang.global.required}">*</span>
    </label>
    <div class='controls'>
      <input class='span4 required' type='text' name='title' id="input-title"
             value='{$title}' required autofocus />
      {if isset($error.title)}<span class="help-inline">{$error.title}</span>{/if}
    </div>
  </div>
  <div class='control-group{if isset($error.content)} error{/if}'>
    <label for='input-content' class='control-label'>
      {$lang.global.description}
    </label>
    <div class='controls'>
      <input class='span4' type='text' name='content' id="input-content" value='{$content}' />
    </div>
  </div>
  <div class='control-group{if isset($error.start_date)} error{/if}'>
    <label for='input-start_date' class='control-label'>
      {$lang.global.date.start} <span title="{$lang.global.required}">*</span>
    </label>
    <div class='controls'>
      <input type='date' name='start_date' id="input-start_date" value='{$start_date}'
             min="{$_date_}" class='span4 required' autocomplete required />
      {if isset($error.start_date)}
        <span class="help-inline">{$error.start_date}</span>
      {/if}
      <p class='help-block'>
        {$lang.calendar.info.date_format}
      </p>
    </div>
  </div>
  <div class='control-group{if isset($error.end_date)} error{/if}'>
    <label for='input-end_date' class='control-label'>
      {$lang.global.date.end}
    </label>
    <div class='controls'>
      <input type='date' name='end_date' id="input-end_date" value='{$end_date}'
             class='span4' min="{$_date_}" autocomplete />
      {if isset($error.end_date)}
        <span class="help-inline">{$error.end_date}</span>
      {/if}
      <p class='help-block'>
        {$lang.calendar.info.date_format}
      </p>
    </div>
  </div>
  <div class='form-actions'>
    <input class='btn btn-primary' type='submit' value="{if $smarty.get.action == 'create'}{$lang.global.create.create}{else}{$lang.global.update.update}{/if}" />
    {if $smarty.get.action == 'update'}
      <input class='btn btn-danger' type='button' value='{$lang.global.destroy.destroy}'
             onclick="candy.system.confirmDestroy('/calendar/{$_request_id_}/destroy')" />
      <input class='btn' type='reset' value='{$lang.global.reset}' />
      <input type='hidden' value='{$_request_id_}' name='id' />
    {/if}
    <input type='hidden' value='formdata' name='{$smarty.get.action}_calendar' />
  </div>
</form>