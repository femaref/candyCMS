<form method='post' action='/user/{$uid}/update'>
  <h2><a href="#">1. {$lang_user_title}</a></h2>
  <div>
    <p {if isset($error_name)}class="error" title="{$error_name}"{/if}>
      <label for='name'>{$lang_name} <span title="{$lang_required}">*</span></label>
      <input name='name' value='{$name}' type='name' required />
    </p>
    <p>
      <label for='surname'>{$lang_surname}</label>
      <input name='surname' value='{$surname}' type='text' />
    </p>
    <p {if isset($error_email)}class="error" title="{$error_email}"{/if}>
      <label for='email'>{$lang_email} <span title="{$lang_required}">*</span></label>
      <input name='email' value='{$email}' type='email' required />
    </p>
    <p>
      <label for='use_gravatar'>{$lang_user_gravatar}</label>
      <input type='checkbox' name='use_gravatar' />
      <span class="description">{$lang_user_gravatar_info}</span>
    </p>
    <p>
      <label for='content'>{$lang_user_content}</label>
      <textarea name='content' rows='6' cols='30'>{$content}</textarea>
    </p>
    <p>
      <label for='receive_newsletter'>{$lang_user_newsletter}</label>
      <input name='receive_newsletter' value='1' type='checkbox' {if $receive_newsletter == 1}checked='checked'{/if} />
    </p>
    {if $USER_RIGHT === 4 && $USER_ID !== $uid}
      <p>
        <label for='user_right'>{$lang_user_right}</label>
        <select name='user_right'>
          <option value='1' {if $user_right == 1}selected{/if}>{$lang_user_right_1}</option>
          <option value='2' {if $user_right == 2}selected{/if}>{$lang_user_right_2}</option>
          <option value='3' {if $user_right == 3}selected{/if}>{$lang_user_right_3}</option>
          <option value='4' {if $user_right == 4}selected{/if}>{$lang_user_right_4}</option>
        </select>
      </p>
    {/if}
    <p class="center">
      <input type='hidden' value='formdata' name='update_user' />
      <input type='submit' value='{$lang_user_submit}' />
    </p>
  </div>
  {if $USER_ID === $uid}
    <h2><a href="#">2. {$lang_password_change}</a></h2>
      <div>
      <p {if isset($error_password_old)}class="error" title="{$error_password_old}"{/if}>
        <label for='password_old'>{$lang_password_old}</label>
        <input name='password_old' type='password' />
      </p>
      <p {if isset($error_password_new)}class="error" title="{$error_password_new}"{/if}>
        <label for='password_new'>{$lang_password_new}</label>
        <input name='password_new' type='password' />
      </p>
      <p>
        <label for='password_new2'>{$lang_password_repeat}</label>
        <input name='password_new2' type='password' />
      </p>
      <p class="center">
        <input type='submit' value='{$lang_password_change}' />
      </p>
    </div>
  {/if}
</form>
<form enctype='multipart/form-data' method='post' action='/user/{$uid}/update'>
  <h2><a href="#">3. {$lang_image_upload}</a></h2>
  <div>
    <p>
      <label for='image'>{$lang_image_choose}</label>
      <input type='file' name='image' accept="image/jpg,image/gif,image/png" />
      <span class="description">{$lang_image_upload_info}</span>
    </p>
    <p>
      <label for='terms'>{$lang_image_terms}</label>
      <input type='checkbox' name='terms' value='1' />
    </p>
    <p class="center">
      <input type='submit' value='{$lang_image_upload}' />
      <input type='hidden' value='formdata' name='create_avatar' />
      <input type='hidden' name='MAX_FILE_SIZE' value='409600' />
    </p>
  </div>
</form>
{if $USER_RIGHT < 4}
  <form enctype='multipart/form-data' method='post' action='/user/{$uid}/destroy'>
    <h2><a href="#">4. {$lang_account_title}</a></h2>
    <div>
      <div class="error">
        {$lang_account_info}
      </div>
      <p>
        <label for='password'>{$lang_password}</label>
        <input name='password' type='password' />
      </p>
      <p class="center">
        <input type='submit' value='{$lang_account_title}' />
        <input type='hidden' value='formdata' name='destroy_user' />
      </p>
    </div>
  </form>
{/if}
<script src='%PATH_PUBLIC%/js/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script type="text/javascript">
  $(document).ready(function(){
    $(".js-fancybox").fancybox();

    $('h2').click(function() {
      $(this).next().slideToggle();
      return false;
    }).next().hide();
  });
</script>