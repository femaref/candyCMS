<form method='post'>
  <h1>{$lang_registration}</h1>
  <p {if isset($error_name)}class="error" title="{$error_name}"{/if}>
    <label for='input-name'>{$lang_name} <span title="{$lang_required}">*</span></label>
    <input name='name' value='{$name}' type='name' id="input-name" autofocus required />
  </p>
  <p>
    <label for='input-surname'>{$lang_surname}</label>
    <input name='surname' value='{$surname}' id="input-surname" type='text' />
  </p>
  <p {if isset($error_email)}class="error" title="{$error_email}"{/if}>
    <label for='input-email'>{$lang_email} <span title="{$lang_required}">*</span></label>
    <input name='email' value='{$email}' type='email' id="input-email" required />
  </p>
  <p {if isset($error_password)}class="error" title="{$error_password}"{/if}>
    <label for='input-password'>{$lang_password} <span title="{$lang_required}">*</span></label>
    <input name='password' type='password' id="input-password" required />
  </p>
  <p>
    <label for='input-password2'>{$lang_password_repeat} <span title="{$lang_required}">*</span></label>
    <input name='password2' type='password' id="input-password2" required />
    <img id="js-icon" src='%PATH_IMAGES%/spacer.png' class="icon-close" alt="" width="16" height="16" />
  </p>
  {if $USER_RIGHT < 4}
    <p {if isset($error_disclaimer)}class="error" title="{$error_disclaimer}"{/if}>
      <label>
        <a href='/help/Registration' id="js-fancybox">
          {$lang_terms_read} <span title="{$lang_required}">*</span>
        </a>
      </label>
      <input name='disclaimer' value='' type='checkbox' required />
    </p>
  {/if}
  <p class="center">
    <input type='submit' value='{$lang_register}' />
    <input type='hidden' value='formdata' name='create_user' />
  </p>
</form>
<script src='%PATH_PUBLIC%/js/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script type="text/javascript">
  $(document).ready(function(){
    $("#js-fancybox").fancybox();

    $("input[name='password2']").keyup(function(){
      if ($("input[name='password']").val() === $("input[name='password2']").val()){
        $('#js-icon').attr('class', 'icon-success');
      } else {
        $('#js-icon').attr('class', 'icon-close');
      }
    });
  });
</script>