<form method='post' action='/user/{$uid}/update'>
  <h1>{$lang_headline}</h1>
  <p {if $error_name}class="error"{/if}>
    <label for='name'>{$lang_name} *</label>
    <input name='name' value='{$name}' type='name' id='name' required />
  </p>
  <p>
    <label for='surname'>{$lang_surname}</label>
    <input name='surname' value='{$surname}' type='text' id='surname' />
  </p>
  <p {if $error_email}class="error"{/if}>
    <label for='email'>{$lang_email} *</label>
    <input name='email' value='{$email}' type='email' id='email' required />
  </p>
  <p>
    <label for='use_gravatar'>{$lang_use_gravatar}</label>
    <input type='checkbox' id='use_gravatar' name='use_gravatar' />
  </p>
  <p>
    {$lang_image_gravatar_info}
    <a href="#" onclick="$('#js-upload_image').toggle(100)">
      {$lang_image_change}
    </a>
  </p>
  <p>
    <label for='description'>{$lang_about_you}</label>
    <textarea name='description' id='description'
              rows='6' cols='30'>{$description}</textarea>
  </p>
  <p>
    <label for='receive_newsletter'>{$lang_newsletter}</label>
    <input name='receive_newsletter' value='1' type='checkbox'
           id='receive_newsletter'
           {if $receive_newsletter == 1}checked='checked'{/if} />
  </p>
  {if $USER_RIGHT == 4 && $USER_ID !== $uid}
    <p>
      <label for='user_right'>{$lang_user_right}</label>
      <select name='user_right' class='inputdropdown'>
        <option value='1' {if $user_right == 1}selected='selectsed'{/if}>{$lang_user_right_1}</option>
        <option value='2' {if $user_right == 2}selected='selectsed'{/if}>{$lang_user_right_2}</option>
        <option value='3' {if $user_right == 3}selected='selectsed'{/if}>{$lang_user_right_3}</option>
        <option value='4' {if $user_right == 4}selected='selectsed'{/if}>{$lang_user_right_4}</option>
      </select>
    </p>
  {/if}
  {if $USER_ID == $uid}
    <h2 style="margin-top:50px">{$lang_password_change}</h2>
    <p {if $error_password_old}class="error"{/if}>
      <label for='password_old'>{$lang_password_old}</label>
      <input name='password_old' value='' type='password' id='password_old' />
    </p>
    <p {if $error_password_new}class="error"{/if}>
      <label for='password_new'>{$lang_password_new}</label>
      <input name='password_new' value='' type='password' id='password_new' />
    </p>
    <p>
      <label for='password_new2'>{$lang_password_repeat}</label>
      <input name='password_new2' value='' type='password' id='password_new2' />
    </p>
  {/if}
  <p class="center">
    <input type='hidden' value='formdata' name='update_user' />
    <input type='submit' class='inputbutton' value='{$lang_submit}' />
  </p>


  {if $USER_ID === $uid}
    <a href='{$avatar_popup}' class="js-fancybox" title='{$name} {$surname}'>
      <img class='image' alt='{$name}' src="{$avatar_100}" />
    </a>
    <br />
    <a href="#" onclick="$('#js-upload_image').toggle(100)">
      <small>{$lang_image_change}</small>
    </a>
  {/if}

</form>
<a name="js-upload_image"></a>
<form id='js-upload_image' style="{$style}" action='/user/{$uid}/update' method='post' enctype='multipart/form-data'>
  <h2>{$lang_image_upload}</h2>
  <p>
    <label for='image'>{$lang_image_choose}</label>
    <input type='file' name='image' id='image' />
    <div class='description'>{$lang_image_upload_info}</div>
  </p>
  <p>
    <label for='agreement'>{$lang_image_agreement}</label>
    <input type='checkbox' id='agreement' name='agreement' value='1' />
  </p>
  <p class="center">
    <input type='submit' value='{$lang_image_upload}' />
    <input type='hidden' value='formdata' name='create_avatar' />
    <input type='hidden' name='MAX_FILE_SIZE' value='409600' />
  </p>
</form>
<script type="text/javascript">
  $(document).ready(function(){
    $(".js-fancybox").fancybox();
  });
</script>