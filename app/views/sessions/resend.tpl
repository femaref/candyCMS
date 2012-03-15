{strip}
  <form method='post' action='/{$_REQUEST.controller}/{$_REQUEST.action}' class='form-horizontal'>
    <div class='page-header'>
      <h1>
        {if $_REQUEST.action == 'verification'}
          {$lang.sessions.verification.title}
        {else}
          {$lang.sessions.password.title}
        {/if}
      </h1>
    </div>
    <p>
      {if $_REQUEST.action == 'verification'}
        {$lang.sessions.verification.info}
      {else}
        {$lang.sessions.password.info}
      {/if}
    </p>
    <div class='control-group{if isset($error.email)} alert alert-error{/if}'>
      <label for='input-email' class='control-label'>
        {$lang.global.email.email} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        <input class='required span4 focused' name='email' type='text'
               title='' id='input-email' autofocus />
        {if isset($error.email)}<span class='help-inline'>{$error.email}</span>{/if}
      </div>
    </div>
    <div class='form-actions'>
      <input type='submit' class='btn btn-primary' value='{$lang.global.submit}' data-theme='b' />
    </div>
  </form>
{/strip}