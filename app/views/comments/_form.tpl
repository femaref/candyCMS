{strip}
  <a name='create'></a>
  <div class='page-header'>
    <h2>
      {$lang.comment.title.create}
    </h2>
  </div>
  <form method='post' data-ajax='false' class='form-horizontal'>
    {if !$_SESSION.user.facebook_id && !$_SESSION.user.name && $_SYSTEM.facebook_plugin == true}
      <p>
        <fb:login-button scope='email' onlogin="window.location='{$CURRENT_URL}#comments'"></fb:login-button>
      </p>
    {/if}
    <div class='control-group{if isset($error.name)} alert alert-error{/if}'>
      <label for='input-name' class='control-label'>
        {$lang.global.name} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        {if $_SESSION.user.name}
          <input type='text' name='name' value="{$_SESSION.user.full_name}" id='input-name'
                class='disabled span4' disabled />
          {if $_SESSION.user.facebook_id}
            <input type='hidden' value="{$_SESSION.user.facebook_id}" name='facebook_id' />
          {/if}
        {else}
          <input type='text' value="{if isset($name)}{$name}{/if}" name='name'
                id='input-name' class='required span4' required />
          {if isset($error.name)}<span class='help-inline'>{$error.name}</span>{/if}
        {/if}
      </div>
    </div>
    <div class='control-group'>
      <label for='input-email' class='control-label'>
        {$lang.global.email.email}
      </label>
      <div class='controls'>
        {if $_SESSION.user.email}
          <input type='text' id='input-email' class='disabled span4' name='email'
                value="{$_SESSION.user.email}" disabled />
        {else}
          <input type='email' class='span4' value="{if isset($email)}{$email}{/if}"
                name='email' id='input-email' />
        {/if}
      </div>
    </div>
    <div class='control-group{if isset($error.content)} alert alert-error{/if}'>
      <label for='js-create_commment_text'>
        {$lang.global.content} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        <textarea name='content' id='js-create_commment_text' rows='5'
                  class='required span4' required>
          {if isset($content)}{$content}{/if}
        </textarea>
        {if isset($error.content)}<span class='help-inline'>{$error.content}</span>{/if}
      </div>
    </div>
    {include file='../layouts/_recaptcha.tpl'}
    <div class='form-actions'>
      <input type='submit' value='{$lang.comment.title.create}' data-theme='b' class='btn btn-primary' />
      <input type='reset' value='{$lang.global.reset}' class='btn' />
      <input type='hidden' value='formdata' name='create_comment' />
      <input type='hidden' value='{$_REQUEST.id}' name='parent_id' />
    </div>
  </form>
{strip}