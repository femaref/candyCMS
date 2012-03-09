{strip}
  <div class='page-header'>
    <h1>{$lang.user.title.update}</h1>
  </div>
  <div class='tabbable'>
    <ul class='nav nav-tabs'>
      <li{if $_REQUEST['action'] == 'update'} class='active'{/if}>
        <a href='#user-personal' data-toggle='tab'>{$lang.user.title.personal_data}</a>
      </li>
      {if $USER_ID == $uid}
        <li{if $_REQUEST['action'] == 'password'} class='active'{/if}>
          <a href='#user-password' data-toggle='tab'>{$lang.user.title.password}</a>
        </li>
      {/if}
      <li{if $_REQUEST['action'] == 'avatar'} class='active'{/if}>
        <a href='#user-image' data-toggle='tab'>{$lang.user.title.image}</a>
      </li>
      {if $USER_ROLE < 4}
        <li{if $_REQUEST['action'] == 'destroy'} class='active'{/if}>
          <a href='#user-destroy' data-toggle='tab'>{$lang.user.title.account}</a>
        </li>
      {/if}
    </ul>
  </div>
  <div class='tab-content'>
    {* Account data *}
    <div class="tab-pane{if $_REQUEST['action'] == 'update'} active{/if}" id='user-personal'>
      <form method='post' action='/user/{$uid}/update' class='form-horizontal'>
        <div class='control-group{if isset($error.name)} alert alert-error{/if}'>
          <label for='input-name' class='control-label'>
            {$lang.global.name} <span title='{$lang.global.required}'>*</span>
          </label>
          <div class='controls'>
            <input class='span4 required' name='name' value="{$name}" type='name'
                  id='input-name' required />
            {if isset($error.name)}<span class='help-inline'>{$error.name}</span>{/if}
          </div>
        </div>
        <div class='control-group'>
          <label for='input-surname' class='control-label'>
            {$lang.global.surname}
          </label>
          <div class='controls'>
            <input class='span4' name='surname' value="{$surname}" type='text'
                  id='input-surname' />
          </div>
        </div>
        <div class='control-group'>
          <label class='control-label'>
            {$lang.global.api_token}
          </label>
          <div class='controls'>
            <span class='uneditable-input span4'>
              {$api_token}
            </span>
          </div>
        </div>
        <div class='control-group'>
          <label for='input-use_gravatar' class='control-label'>
            {$lang.user.label.gravatar}
          </label>
          <div class='controls'>
            <input type='checkbox' class='checkbox' name='use_gravatar' id='input-use_gravatar'
                    {if $use_gravatar == 1}checked{/if} />
            <p class='help-block'>
              {$lang.user.info.gravatar}
            </p>
          </div>
        </div>
        <div class='control-group'>
          <label for='input-content' class='control-label'>
            {$lang.user.label.content.update}
          </label>
          <div class='controls'>
            <textarea name='content' rows='6' class='span4' id='input-content'>
              {$content}
            </textarea>
            <span class='help-inline'></span>
          </div>
        </div>
        <div class='control-group'>
          <label for='input-receive_newsletter' class='control-label'>
            {$lang.user.label.newsletter}
          </label>
          <div class='controls'>
            <input name='receive_newsletter' id='input-receive_newsletter' value='1'
                    type='checkbox' class='checkbox' {if $receive_newsletter == 1}checked{/if} />
          </div>
        </div>
        {if $USER_ROLE == 4 && $USER_ID !== $uid}
          <div class='control-group'>
            <label for='input-role' class='control-label'>
              {$lang.global.user.role}
            </label>
            <div class='controls'>
              <select name='role' id='input-role' class='span4'>
                <option value='1'{if $role == 1} selected{/if}>{$lang.global.user.roles.1}</option>
                <option value='2'{if $role == 2} selected{/if}>{$lang.global.user.roles.2}</option>
                <option value='3'{if $role == 3} selected{/if}>{$lang.global.user.roles.3}</option>
                <option value='4'{if $role == 4} selected{/if}>{$lang.global.user.roles.4}</option>
              </select>
            </div>
          </div>
        {/if}
        <div class='form-actions'>
          <input type='submit' class='btn btn-primary' value='{$lang.user.label.update}' />
          <input type='reset' class='btn' value='{$lang.global.reset}' />
          <input type='hidden' value="{$email}" name='email' />
          <input type='hidden' value='formdata' name='update_user' />
        </div>
      </form>
    </div>

  {* Password *}
  {if $USER_ID == $uid}
    <div class="tab-pane{if $_REQUEST['action'] == 'password'} active{/if}" id='user-password'>
      <form method='post' action='/user/{$uid}/password' class='form-horizontal'>
        <div class='control-group{if isset($error.password_old)} alert alert-error{/if}'>
          <label for='input-password_old' class='control-label'>
            {$lang.user.label.password.old} <span title='{$lang.global.required}'>*</span>
          </label>
          <div class='controls'>
            <input name='password_old' id='input-password_old' type='password'
                  class='span4 required' required />
            {if isset($error.password_old)}<span class='help-inline'>{$error.password_old}</span>{/if}
          </div>
        </div>
        <div class='control-group{if isset($error.password_new)} alert alert-error{/if}'>
          <label for='input-password_new' class='control-label'>
            {$lang.user.label.password.new} <span title='{$lang.global.required}'>*</span>
          </label>
          <div class='controls'>
            <input name='password_new' id='input-password_new' type='password'
                    class='span4 required' required />
            {if isset($error.password_new)}<span class='help-inline'>{$error.password_new}</span>{/if}
          </div>
        </div>
        <div class='control-group'>
          <label for='input-password_new2' class='control-label'>
            {$lang.global.password.repeat} <span title='{$lang.global.required}'>*</span>
          </label>
          <div class='controls'>
            <input name='password_new2' id='input-password_new2' type='password'
                  class='span4 required' required />
          </div>
        </div>
        <div class='form-actions'>
          <input type='submit' class='btn btn-primary' value='{$lang.user.label.password.create}' />
          <input type='reset' class='btn' value='{$lang.global.reset}' />
        </div>
      </form>
    </div>
  {/if}

  {* Avatar *}
    <div class="tab-pane{if $_REQUEST['action'] == 'avatar'} active{/if}" id='user-image'>
      <form enctype='multipart/form-data' method='post' action='/user/{$uid}/avatar'
            class='form-horizontal'>
        <div class='control-group{if isset($error.image)} alert alert-error{/if}'>
          <label for='input-image' class='control-label'>
            {$lang.user.label.image.choose}
          </label>
          <div class='controls'>
            <input type='file' name='image' id='input-image' class='span4'
                  accept='image/jpg,image/gif,image/png' />
            {if isset($error.image)}
              <span class='help-inline'>{$error.image}</span>
            {/if}
            <span class='help-block'>
              {$lang.user.info.image}
            </span>
          </div>
        </div>
        <div class='control-group{if isset($error.terms)} alert alert-error{/if}'>
          <label for='input-terms' class='control-label'>
            {$lang.global.terms.terms}
          </label>
          <div class='controls'>
            <label class='checkbox'>
              <input type='checkbox' class='checkbox' name='terms'
                    id='input-terms' value='1' />
                {$lang.user.label.image.terms}
            </label>
            {if isset($error.terms)}
              <span class='help-inline'>{$error.terms}</span>
            {/if}
          </div>
        </div>
        <div class='form-actions'>
          <input type='submit' class='btn btn-primary' value='{$lang.user.title.image}' />
          <input type='reset' class='btn' value='{$lang.global.reset}' />
          <input type='hidden' value='formdata' name='create_avatar' />
          <input type='hidden' name='MAX_FILE_SIZE' value='409600' />
        </div>
      </form>
    </div>

  {* Destroy account *}
  {if $USER_ROLE < 4}
    <div class="tab-pane{if $_REQUEST['action'] == 'destroy'} active{/if}" id='user-destroy'>
      <form method='post' action='/user/{$uid}/destroy' class='form-horizontal'>
        <p class='alert alert-danger'>
          {$lang.user.info.destroy_account}
        </p>
        <div class='control-group'>
          <label for='input-password' class='control-label'>
            {$lang.global.password.password}
          </label>
          <div class='controls'>
            <input name='password' type='password' id='input-password' />
          </div>
        </div>
        <div class='form-actions'>
          <input type='submit' class='btn btn-danger' value='{$lang.user.label.account.destroy}' />
          <input type='hidden' value='formdata' name='destroy_user' />
        </div>
      </form>
    </div>
  {/if}
  <script type='text/javascript' src='%PATH_JS%/core/jquery.bootstrap.tabs{$_compress_files_suffix_}.js'></script>
  <script type='text/javascript'>
    $('#input-content').bind('keyup', function() {
      countCharLength(this, 1000);
    });
  </script>
{/strip}