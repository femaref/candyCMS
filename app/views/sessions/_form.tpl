{strip}
  <form action='/{$_REQUEST.controller}/create' method='post' data-ajax='false' class='form-horizontal'>
    {if !$MOBILE}
      <div class='page-header'>
        <h1>{$lang.global.login}</h1>
      </div>
    {/if}
    <div class='control-group{if isset($error.email)} alert alert-error{/if}'>
      <label for='input-email' class='control-label'>
        {$lang.global.email.email} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        <input name='email' class='focused required span4' type='email' value='{$email}'
              id='input-email' autofocus required />
        {if isset($error.email)}<span class='help-inline'>{$error.email}</span>{/if}
      </div>
    </div>
    <div class='control-group{if isset($error.password)} alert alert-error{/if}'>
      <label for='input-password' class='control-label'>
        {$lang.global.password.password} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        <input class='required span4' name='password' type='password'
              id='input-password' required />
        {if isset($error.password)}<span class='help-inline'>{$error.password}</span>{/if}
      </div>
    </div>
    <div class='form-actions'>
      <input type='submit' value='{$lang.global.login}' data-theme='b' class='btn btn-primary'/>
      <input type='hidden' value='formdata' name='create_sessions' />
    </div>
  </form>
  <div class='center' data-role="controlgroup">
    <a href='/{$_REQUEST.controller}/password' class='btn' data-role="button">{$lang.sessions.password.title}</a>
    <a href='/{$_REQUEST.controller}/verification' class='btn' data-role="button">{$lang.sessions.verification.title}</a>
  </div>
{/strip}