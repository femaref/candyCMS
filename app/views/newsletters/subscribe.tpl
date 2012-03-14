{strip}
  <div class='page-header'>
    <h1>{$lang.newsletter.title.subscribe}</h1>
  </div>
  <p>
    {$lang.newsletter.info.subscribe}
  </p>
  <form method='post' class='form-horizontal'>
    <div class='control-group'>
      <label for='input-name' class='control-label'>
        {$lang.global.name}
      </label>
      <div class='controls'>
        <input name='name' type='text' id='input-name' class='span4' autofocus />
      </div>
    </div>
    <div class='control-group'>
      <label for='input-surname' class='control-label'>
        {$lang.global.surname}
      </label>
      <div class='controls'>
        <input name='surname' id='input-surname' type='text' class='span4' />
      </div>
    </div>
    <div class='control-group{if isset($error.email)} alert alert-error{/if}'>
      <label for='input-email' class='control-label'>
        {$lang.global.email.email} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        <input type='email' name='email' id='input-email'
              class='span4 required focused' required />
        {if isset($error.email)}<span class='help-inline'>{$error.email}</span>{/if}
      </div>
    </div>
    <div class='form-actions'>
      <input type='submit' class='btn btn-primary' value='{$lang.newsletter.title.subscribe}' />
      <input type='hidden' value='formdata' name='subscribe_newsletter' />
    </div>
  </form>
{/strip}