{strip}
  <form method='post' action='/session/{$smarty.get.action}' class='form-horizontal'>
    <div class='page-header'>
      <h1>
        {if $smarty.get.action == 'verification'}
          {$lang.session.verification.title}
        {else}
          {$lang.session.password.title}
        {/if}
      </h1>
    </div>
    <p>
      {if $smarty.get.action == 'verification'}
        {$lang.session.verification.info}
      {else}
        {$lang.session.password.info}
      {/if}
    </p>
    <div class='control-group{if isset($error.email)} error{/if}'>
      <label for='input-email' class='control-label'>
        {$lang.global.email.email} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        <input class='required span4' name='email' type='email' title='' id='input-email' autofocus required />
        {if isset($error.email)}<span class='help-inline'>{$error.email}</span>{/if}
      </div>
    </div>
    <div class='form-actions'>
      <input type='submit' class='btn btn-primary' value='{$lang.global.submit}' data-theme='b' />
    </div>
  </form>
{/strip}