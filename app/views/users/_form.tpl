<form method='post' action='/user/{$uid}/update'>
  <h2><a href="#">1. _Stammdaten_</a></h2>
  <div>
    <p {if $error_name}class="error"{/if}>
      <label for='name'>{$lang_name} *</label>
      <input name='name' value='{$name}' type='name' required />
    </p>
    <p>
      <label for='surname'>{$lang_surname}</label>
      <input name='surname' value='{$surname}' type='text' />
    </p>
    <p {if $error_email}class="error"{/if}>
      <label for='email'>{$lang_email} *</label>
      <input name='email' value='{$email}' type='email' required />
    </p>
    <p>
      <label for='use_gravatar'>{$lang_use_gravatar}</label>
      <input type='checkbox' name='use_gravatar' />
      <span class="description">{$lang_image_gravatar_info}</span>
    </p>
    <p>
      <label for='description'>{$lang_about_you}</label>
      <textarea name='description' rows='6' cols='30'>{$description}</textarea>
    </p>
    <p>
      <label for='receive_newsletter'>{$lang_newsletter}</label>
      <input name='receive_newsletter' value='1' type='checkbox' {if $receive_newsletter == 1}checked='checked'{/if} />
    </p>
    {if $USER_RIGHT === 4 && $USER_ID !== $uid}
      <p>
        <label for='user_right'>{$lang_user_right}</label>
        <select name='user_right'>
          <option value='1' {if $user_right == 1}selected='selectsed'{/if}>{$lang_user_right_1}</option>
          <option value='2' {if $user_right == 2}selected='selectsed'{/if}>{$lang_user_right_2}</option>
          <option value='3' {if $user_right == 3}selected='selectsed'{/if}>{$lang_user_right_3}</option>
          <option value='4' {if $user_right == 4}selected='selectsed'{/if}>{$lang_user_right_4}</option>
        </select>
      </p>
    {/if}
    <p class="center">
      <input type='hidden' value='formdata' name='update_user' />
      <input type='submit' class='inputbutton' value='{$lang_submit}' />
    </p>
  </div>
  {if $USER_ID === $uid}
    <h2><a href="#">2. {$lang_password_change}</a></h2>
      <div>
      <p {if $error_password_old}class="error"{/if}>
        <label for='password_old'>{$lang_password_old}</label>
        <input name='password_old' type='password' />
      </p>
      <p {if $error_password_new}class="error"{/if}>
        <label for='password_new'>{$lang_password_new}</label>
        <input name='password_new' type='password' />
      </p>
      <p>
        <label for='password_new2'>{$lang_password_repeat}</label>
        <input name='password_new2' type='password' />
      </p>
      <p class="center">
        <input type='hidden' value='formdata' name='update_user' />
        <input type='submit' class='inputbutton' value='{$lang_submit}' />
      </p>
    </div>
  {/if}
  <h2><a href="#">3. {$lang_image_upload}</a></h2>
  <div>
    <p>
      <label for='image'>{$lang_image_choose}</label>
      <input type='file' name='image' title='' />
      <span class="description">{$lang_image_upload_info}</span>
    </p>
    <p>
      <label for='agreement'>{$lang_image_agreement}</label>
      <input type='checkbox' name='agreement' value='1' />
    </p>
    <p class="center">
      <input type='submit' value='{$lang_image_upload}' />
      <input type='hidden' value='formdata' name='create_avatar' />
      <input type='hidden' name='MAX_FILE_SIZE' value='409600' />
    </p>
  </div>
</form>
<script language='javascript' src='%PATH_PUBLIC%/js/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script type="text/javascript">
  $(document).ready(function(){
    $(".js-fancybox").fancybox();

    $('h2').click(function() {
      $(this).next().slideToggle();
      return false;
    }).next().hide();
  });
</script>