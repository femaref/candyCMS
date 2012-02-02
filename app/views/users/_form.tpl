<ul class='tabs' data-tabs='tabs'>
  <li class='active'>
    <a href='#personal'>{$lang.user.title.personal_data}</a>
  </li>
  {if $USER_ID == $uid}
    <li>
      <a href='#password'>{$lang.user.title.password}</a>
    </li>
  {/if}
  <li>
    <a href='#image'>{$lang.user.title.image}</a>
  </li>
  {if $USER_ROLE < 4}
    <li>
      <a href='#destroy'>{$lang.user.title.account}</a>
    </li>
  {/if}
</ul>


<div class='pill-content'>
{* Account data *}
  <div class='active row' id='personal'>
    <div class='span4'>
      <h2>{$lang.user.title.personal_data}</h2>
    </div>
    <form method='post' action='/user/{$uid}/update' class='span12'>
      <fieldset>
        <div class='clearfix{if isset($error.name)} error{/if}'>
          <label for='input-name'>{$lang.global.name} <span title='{$lang.global.required}'>*</span></label>
          <div class='input'>
            <input name='name' value='{$name}' type='name' id='input-name' required />
            {if isset($error.name)}<span class='help-inline'>{$error.name}</span>{/if}
          </div>
        </div>
        <div class='clearfix'>
          <label for='input-surname'>{$lang.global.surname}</label>
          <div class='input'>
            <input name='surname' value='{$surname}' type='text' id='input-surname' />
          </div>
        </div>
        <div class='clearfix'>
          <label for='input-api_token'>{$lang.global.api_token}</label>
          <div class='input'>
            <span>  
              {$api_token}
            </span>
          </div>
        </div>
        <div class='clearfix'>
          <label for='input-use_gravatar'>{$lang.user.label.gravatar}</label>
          <div class='input'>
            <ul class='inputs-list'>
              <li>
                <label>
                  <input type='checkbox' name='use_gravatar' id='input-use_gravatar' {if $use_gravatar == 1}checked{/if} />
                  <span class='description'>{$lang.user.info.gravatar}</span>
                </label>
              </li>
            </ul>
          </div>
        </div>
        <div class='clearfix'>
          <label for='input-content'>{$lang.user.label.content.update}</label>
          <div class='input'>
            <textarea name='content' rows='6' class='xxlarge' id='input-content'>{$content}</textarea>
          </div>
        </div>
        <div class='clearfix'>
          <label for='input-receive_newsletter'>{$lang.user.label.newsletter}</label>
          <div class='input'>
            <ul class='inputs-list'>
              <li>
                <label>
                  <input name='receive_newsletter' id='input-receive_newsletter' value='1'
                         type='checkbox' {if $receive_newsletter == 1}checked{/if} />
                </label>
              </li>
            </ul>
          </div>
        </div>
        {if $USER_ROLE == 4 && $USER_ID !== $uid}
          <div class='clearfix'>
            <label for='input-role'>{$lang.global.user.role}</label>
            <div class='input'>
              <select name='role' id='input-role'>
                <option value='1' {if $role == 1}selected{/if}>{$lang.global.user.roles.1}</option>
                <option value='2' {if $role == 2}selected{/if}>{$lang.global.user.roles.2}</option>
                <option value='3' {if $role == 3}selected{/if}>{$lang.global.user.roles.3}</option>
                <option value='4' {if $role == 4}selected{/if}>{$lang.global.user.roles.4}</option>
              </select>
            </div>
          </div>
        {/if}
        <div class='actions'>
          <input type='hidden' value='{$email}' name='email' />
          <input type='hidden' value='formdata' name='update_user' />
          <input type='submit' class='btn primary' value='{$lang.user.label.update}' />
        </div>
      </fieldset>
    </form>
  </div>
  
{* Password *}
{if $USER_ID == $uid}
  <div class='row' id='password'>
    <div class='span4'>
      <h2>{$lang.user.title.password}</h2>
    </div>
    <form method='post' action='/user/{$uid}/password' class='span12'>
      <fieldset>
        <div class='clearfix{if isset($error.password_old)} error{/if}'>
          <label for='input-password_old'>{$lang.user.label.password.old} <span title='{$lang.global.required}'>*</span></label>
          <div class='input'>
            <input name='password_old' id='input-password_old' type='password' required />
            {if isset($error.password_old)}<span class='help-inline'>{$error.password_old}</span>{/if}
          </div>
        </div>
        <div class='clearfix{if isset($error.password_new)} error{/if}'>
          <label for='input-password_new'>{$lang.user.label.password.new} <span title='{$lang.global.required}'>*</span></label>
          <div class='input'>
            <input name='password_new' id='input-password_new' type='password' required />
            {if isset($error.password_old)}<span class='help-inline'>{$error.password_new}</span>{/if}
          </div>
        </div>
        <div class='clearfix'>
          <label for='input-password_new2'>{$lang.global.password.repeat} <span title='{$lang.global.required}'>*</span></label>
          <div class='input'>
            <input name='password_new2' id='input-password_new2' type='password' required />
          </div>
        </div>
        <div class='actions'>
          <input type='submit' class='btn primary' value='{$lang.user.label.password.create}' />
        </div>
      </fieldset>
    </form>
  </div>
{/if}

{* Avatar *}
  <div class='row' id='image'>
    <div class='span4'>
      <h2>{$lang.user.title.image}</h2>
    </div>
    <form enctype='multipart/form-data' method='post' action='/user/{$uid}/avatar' class='span12'>
      <fieldset>
        <div class='clearfix{if isset($error.image)} error{/if}'>
          <label for='input-image'>{$lang.user.label.image.choose}</label>
          <div class='input'>
            <input type='file' name='image' id='input-image' accept='image/jpg,image/gif,image/png' />
            <span class='help-inline'>{$lang.user.info.image}</span>
            {if isset($error.image)}<span class='help-inline'>{$error.image}</span>{/if}
          </div>
        </div>
        <div class='clearfix{if isset($error.terms)} error{/if}'>
          <label for='input-terms'>{$lang.global.terms.terms}</label>
          <div class='input'>
            <ul class='inputs-list'>
              <li>
                <label>
                  <input type='checkbox' name='terms' id='input-terms' value='1' />
                  <span class='help-inline'>{$lang.user.label.image.terms}</span>
                  {if isset($error.terms)}<span class='help-inline'>{$error.terms}</span>{/if}
                </label>
              </li>
            </ul>
          </div>
        </div>
        <div class='actions'>
          <input type='submit' class='btn primary' value='{$lang.user.label.image.create}' />
          <input type='hidden' value='formdata' name='create_avatar' />
          <input type='hidden' name='MAX_FILE_SIZE' value='409600' />
        </div>
      </fieldset>
    </form>
  </div>
  
{* Destroy account *}
{if $USER_ROLE < 4}
  <div class='row' id='destroy'>
    <div class='span4'>
      <h2>{$lang.user.title.account}</h2>
    </div>
    <form enctype='multipart/form-data' method='post' action='/user/{$uid}/destroy' class='span12'>
      <fieldset>
        <p>
          {$lang.user.info.destroy_account}
        </p>
        <div class='clearfix'>
          <label for='input-password'>{$lang.global.password.password}</label>
          <div class='input'>
            <input name='password' type='password' id='input-password' />
          </div>
        </div>
        <div class='actions'>
          <input type='submit' class='btn primary' value='{$lang.user.label.account.destroy}' />
          <input type='hidden' value='formdata' name='destroy_user' />
        </div>
      </fieldset>
    </form>
  </div>
{/if}

<script type='text/javascript' src='%PATH_JS%/core/jquery.fancybox{$_compress_files_suffix_}.js'></script>
<script type='text/javascript' src='%PATH_JS%/core/jquery.tiptip{$_compress_files_suffix_}.js'></script>
<script type="text/javascript">
  $(document).ready(function(){
    $(".js-fancybox").fancybox();
    $('.tabs').tabs();
    $('p.error').tipTip({ maxWidth: "auto" });
  });
</script>