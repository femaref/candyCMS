{strip}
  <div class='page-header'>
    <h1>{$lang.users.title.update}</h1>
  </div>
  <div class='tabbable'>
    <ul class='nav nav-tabs'>
      <li{if $_REQUEST['action'] == 'update'} class='active'{/if}>
        <a href='#user-personal' data-toggle='tab'>{$lang.users.title.personal_data}</a>
      </li>
      {if $_SESSION.user.id == $uid}
        <li{if $_REQUEST['action'] == 'password'} class='active'{/if}>
          <a href='#user-password' data-toggle='tab'>{$lang.users.title.password}</a>
        </li>
      {/if}
      <li id='js-avatar_tab'
          {if $_REQUEST['action'] == 'avatar' && $use_gravatar == 1}class='active hide'
          {elseif $use_gravatar == 1}class='hide'
          {elseif $_REQUEST['action'] == 'avatar'}class='active'{/if}>
        <a href='#user-image' data-toggle='tab'>{$lang.users.title.image}</a>
      </li>
      {if $_SESSION.user.role < 4}
        <li{if $_REQUEST['action'] == 'destroy'} class='active'{/if}>
          <a href='#user-destroy' data-toggle='tab'>{$lang.users.title.account}</a>
        </li>
      {/if}
    </ul>
  </div>
  <div class='tab-content'>
    {* Account data *}
    <div class="tab-pane{if $_REQUEST['action'] == 'update'} active{/if}" id='user-personal'>
      <form method='post' action='/{$_REQUEST.controller}/{$uid}/update' class='form-horizontal'>
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
            {$lang.users.label.gravatar}
          </label>
          <div class='controls'>
            <input type='checkbox'
                   class='checkbox'
                   name='use_gravatar'
                   id='input-use_gravatar'
                   {if $use_gravatar == 1}checked{/if} />
            <div class='help-inline'>
              <a href='{$avatar_popup}'
                class='thumbnail js-fancybox'
                title='{$full_name}'
                id='js-gravatar'
                style='{if $use_gravatar == 0}opacity:0.25{/if}'>
                <img alt='{$name} {$surname}' src='{$avatar_32}' width='32' height='32' />
              </a>
            </div>
            <p id='js-gravatar_help' class='help-block{if $use_gravatar == 1} hide{/if}'>
              {$lang.users.info.gravatar}
            </p>
          </div>
        </div>
        <div class='control-group'>
          <label for='input-content' class='control-label'>
            {$lang.users.label.content.update}
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
            {$lang.users.label.newsletter}
          </label>
          <div class='controls'>
            <input name='receive_newsletter' id='input-receive_newsletter' value='1'
                    type='checkbox' class='checkbox' {if $receive_newsletter == 1}checked{/if} />
          </div>
        </div>
        {if $_SESSION.user.role == 4 && $_SESSION.user.id !== $uid}
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
          <input type='submit' class='btn btn-primary' value='{$lang.users.label.update}' />
          <input type='reset' class='btn' value='{$lang.global.reset}' />
          <input type='hidden' value="{$email}" name='email' />
          <input type='hidden' value='formdata' name='update_users' />
        </div>
      </form>
    </div>

  {* Password *}
  {if $_SESSION.user.id == $uid}
    <div class="tab-pane{if $_REQUEST['action'] == 'password'} active{/if}" id='user-password'>
      <form method='post' action='/{$_REQUEST.controller}/{$uid}/password' class='form-horizontal'>
        <div class='control-group{if isset($error.password_old)} alert alert-error{/if}'>
          <label for='input-password_old' class='control-label'>
            {$lang.users.label.password.old} <span title='{$lang.global.required}'>*</span>
          </label>
          <div class='controls'>
            <input name='password_old' id='input-password_old' type='password'
                  class='span4 required' required />
            {if isset($error.password_old)}<span class='help-inline'>{$error.password_old}</span>{/if}
          </div>
        </div>
        <div class='control-group{if isset($error.password_new)} alert alert-error{/if}'>
          <label for='input-password_new' class='control-label'>
            {$lang.users.label.password.new} <span title='{$lang.global.required}'>*</span>
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
          <input type='submit' class='btn btn-primary' value='{$lang.users.label.password.create}' />
          <input type='reset' class='btn' value='{$lang.global.reset}' />
        </div>
      </form>
    </div>
  {/if}

  {* Avatar *}
    <div class="tab-pane{if $_REQUEST['action'] == 'avatar'} active{/if}" id='user-image'>
      <form enctype='multipart/form-data' method='post' action='/{$_REQUEST.controller}/{$uid}/avatar'
            class='form-horizontal'>
        <div class='control-group{if isset($error.image)} alert alert-error{/if}'>
          <label for='input-image' class='control-label'>
            {$lang.users.label.image.choose}
          </label>
          <div class='controls'>
            <input type='file' name='image' id='input-image' class='span4'
                  accept='image/jpg,image/gif,image/png' />
            {if isset($error.image)}
              <span class='help-inline'>{$error.image}</span>
            {/if}
            <span class='help-block'>
              {$lang.users.info.image}
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
                {$lang.users.label.image.terms}
            </label>
            {if isset($error.terms)}
              <span class='help-inline'>{$error.terms}</span>
            {/if}
          </div>
        </div>
        <div class='form-actions'>
          <input type='submit' class='btn btn-primary' value='{$lang.users.title.image}' />
          <input type='reset' class='btn' value='{$lang.global.reset}' />
          <input type='hidden' value='formdata' name='create_avatar' />
          <input type='hidden' name='MAX_FILE_SIZE' value='409600' />
        </div>
      </form>
    </div>

  {* Destroy account *}
  {if $_SESSION.user.role < 4}
    <div class="tab-pane{if $_REQUEST['action'] == 'destroy'} active{/if}" id='user-destroy'>
      <form method='post' action='/{$_REQUEST.controller}/{$uid}/destroy' class='form-horizontal'>
        <p class='alert alert-danger'>
          {$lang.users.info.destroy_account}
        </p>
        <div class='control-group'>
          <label for='input-password' class='control-label'>
            {$lang.global.password.password}
          </label>
          <div class='controls'>
            <input name='password' type='password' id='input-password' class='span4' />
          </div>
        </div>
        <div class='form-actions'>
          <input type='submit' class='btn btn-danger' value='{$lang.users.label.account.destroy}' />
          <input type='hidden' value='formdata' name='destroy_users' />
        </div>
      </form>
    </div>
  {/if}
  <script src='{$_PATH.js}/core/jquery.fancybox{$_SYSTEM.compress_files_suffix}.js' type='text/javascript'></script>
  <script type='text/javascript' src='{$_PATH.js}/core/jquery.bootstrap.tabs{$_SYSTEM.compress_files_suffix}.js'></script>
  <script type='text/javascript'>
    $('#input-use_gravatar').change(function() {
      var avatarIsChecked = $(this).is(':checked');
      $('#js-gravatar').toggleOpacity(avatarIsChecked);
      $('#js-gravatar_help').toggle('fast');
      $('#js-avatar_tab').toggle(avatarIsChecked);
    });

    $('#input-content').bind('keyup', function() {
      countCharLength(this, 1000);
    });

    $('.js-fancybox').fancybox();
  </script>
{/strip}