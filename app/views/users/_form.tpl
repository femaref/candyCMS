<form method='post' action='/user/{$uid}/update'>
  <h2><a href="#">{$lang.user.title.personal_data}</a></h2>
  <div>
    <p {if isset($error.name)}class="error" title="{$error.name}"{/if}>
      <label for='input-name'>{$lang.global.name}<span title="{$lang.global.required}">*</span></label>
      <input name='name' value='{$name}' type='name' id="input-name" required />
    </p>
    <p>
      <label for='input-surname'>{$lang.global.surname}</label>
      <input name='surname' value='{$surname}' type='text' id="input-surname" />
    </p>
    <p {if isset($error.email)}class="error" title="{$error.email}"{/if}>
      <label for='input-email'>{$lang.global.email.email}<span title="{$lang.global.required}">*</span></label>
      <input name='email' value='{$email}' type='email' id="input-email" required />
    </p>
    <p>
      <label for='input-use_gravatar'>{$lang.user.label.gravatar}</label>
      <input type='checkbox' name='use_gravatar' id="input-use_gravatar"
             {if $use_gravatar == 1}checked{/if} />
      <span class="description">{$lang.user.info.gravatar}</span>
    </p>
    <p>
      <label for='input-content'>{$lang.user.label.content.update}</label>
      <textarea name='content' rows='6' cols='30' id="input-content">{$content}</textarea>
    </p>
    <p>
      <label for='input-receive_newsletter'>{$lang.user.label.newsletter}</label>
      <input name='receive_newsletter' id="input-receive_newsletter" value='1'
             type='checkbox' {if $receive_newsletter == 1}checked{/if} />
    </p>
    {if $USER_RIGHT === 4 && $USER_ID !== $uid}
      <p>
        <label for='input-user_right'>{$lang.global.user.right}</label>
        <select name='user_right' id="input-user_right">
          <option value='1' {if $user_right == 1}selected{/if}>{$lang.global.user.rights.1}</option>
          <option value='2' {if $user_right == 2}selected{/if}>{$lang.global.user.rights.2}</option>
          <option value='3' {if $user_right == 3}selected{/if}>{$lang.global.user.rights.3}</option>
          <option value='4' {if $user_right == 4}selected{/if}>{$lang.global.user.rights.4}</option>
        </select>
      </p>
    {/if}
    <p class="center">
      <input type='hidden' value='formdata' name='update_user' />
      <input type='submit' value='{$lang.user.label.update}' />
    </p>
  </div>
  {if $USER_ID === $uid}
    <h2><a href="#">{$lang.user.title.password}</a></h2>
      <div>
      <p {if isset($error.password_old)}class="error" title="{$error.password_old}"{/if}>
        <label for='input-password_old'>{$lang.user.label.password.old}</label>
        <input name='password_old' id="input-password_old" type='password' />
      </p>
      <p {if isset($error.password_new)}class="error" title="{$error.password_new}"{/if}>
        <label for='input-password_new'>{$lang.user.label.password.new}</label>
        <input name='password_new' id="input-password_new" type='password' />
      </p>
      <p>
        <label for='input-password_new2'>{$lang.global.password.repeat}</label>
        <input name='password_new2' id="input-password_new2" type='password' />
      </p>
      <p class="center">
        <input type='submit' value='{$lang.user.label.password.create}' />
      </p>
    </div>
  {/if}
</form>
<form enctype='multipart/form-data' method='post' action='/user/{$uid}/update'>
  <h2><a href="#">{$lang.user.title.image}</a></h2>
  <div>
    <p {if isset($error.image)}class="error" title="{$error.image}"{/if}>
      <label for='input-image'>{$lang.user.label.image.choose}</label>
      <input type='file' name='image' id="input-image" accept="image/jpg,image/gif,image/png" />
      <span class="description">{$lang.user.info.image}</span>
    </p>
    <p {if isset($error.terms)}class="error" title="{$error.terms}"{/if}>
      <label for='input-terms'>{$lang.user.label.image.terms}</label>
      <input type='checkbox' name='terms' id="input-terms" value='1' />
    </p>
    <p class="center">
      <input type='submit' value='{$lang.user.label.image.create}' />
      <input type='hidden' value='formdata' name='create_avatar' />
      <input type='hidden' name='MAX_FILE_SIZE' value='409600' />
    </p>
  </div>
</form>
{if $USER_RIGHT < 4}
  <form enctype='multipart/form-data' method='post' action='/user/{$uid}/destroy'>
    <h2><a href="#">{$lang.user.title.account}</a></h2>
    <div>
      <div class="error">
        {$lang.user.info.account}
      </div>
      <p>
        <label for='input-password'>{$lang.global.password.password}</label>
        <input name='password' type='password' id="input-password" />
      </p>
      <p class="center">
        <input type='submit' value='{$lang.user.label.account.destroy}' />
        <input type='hidden' value='formdata' name='destroy_user' />
      </p>
    </div>
  </form>
{/if}
<script src='%PATH_JS%/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script type="text/javascript">
  $(document).ready(function(){
    $(".js-fancybox").fancybox();

    $('h2').click(function() {
      $(this).next().slideToggle();
      return false;
    }).next().hide();
  });
</script>