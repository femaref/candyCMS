{strip}
  <div class='page-header'>
    <h1>{$lang.global.contact} {$contact.name} {$contact.surname}</h1>
  </div>
  <form method='post' action='/{$_REQUEST.controller}/{$_REQUEST.id}'
        id='create_mail' class='form-horizontal'>
    <div class='control-group{if isset($error.email)} alert alert-error{/if}'>
      <label for='input-email' class='control-label'>
        {$lang.global.email.email} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        <input id='input-email' class='required span4' name='email'
              value="{$email}" type='email' required />
        {if isset($error.email)}<span class='help-inline'>{$error.email}</span>{/if}
      </div>
    </div>
    <div class='control-group'>
      <label for='input-subject' class='control-label'>{$lang.global.subject}</label>
      <div class='controls'>
        <input id='input-subject' class='span4' name='subject' value="{$subject}" type='text' />
      </div>
    </div>
    <div class='control-group{if isset($error.content)} alert alert-error{/if}'>
      <label for='input-content' class='control-label'>
        {$lang.global.content} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        <textarea class='required span5' id='input-content' name='content'
                  rows='6' required>{$content}</textarea>
        {if isset($error.content)}<span class='help-inline'>{$error.content}</span>{/if}
      </div>
    </div>
    {include file='../layouts/_recaptcha.tpl'}
    <div class='form-actions'>
      <input type='submit' class='btn btn-primary' value='{$lang.global.submit}' />
      <input type='hidden' value='formdata' name='create_mail' />
    </div>
  </form>
{/strip}