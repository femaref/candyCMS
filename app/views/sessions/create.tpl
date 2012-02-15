{strip}
  <form method='post' data-ajax='false' class='form-horizontal'>
    <div class='page-header'>
      <h1>{$lang.global.login}</h1>
    </div>
    <div class='control-group{if isset($error.email)} error{/if}'>
      <label for='input-email' class='control-label'>
        {$lang.global.email.email} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        <input name='email' class='required span4' type='email' value="{$email}"
              id='input-email' autofocus required />
        {if isset($error.email)}<span class='help-inline'>{$error.name}</span>{/if}
      </div>
    </div>
    <div class='control-group{if isset($error.password)} error{/if}'>
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
      <input type='hidden' value='formdata' name='create_session' />
    </div>
  </form>
  <div class='center'>
    <a href='/session/password' class='btn'>{$lang.session.password.title}</a>
    <a href='/session/verification' class='btn'>{$lang.session.verification.title}</a>
  </div>
{/strip}