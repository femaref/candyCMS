<form method='post'>
  <h1>{$lang_registration}</h1>
  <p {if isset($error_name)}class="error"{/if}>
    <label for='name'>{$lang_name} *</label>
    <input name='name' value='{$name}' type='name' autofocus required />
  </p>
  <p>
    <label for='surname'>{$lang_surname}</label>
    <input name='surname' value='{$surname}' type='text' />
  </p>
  <p {if isset($error_email)}class="error"{/if}>
    <label for='email'>{$lang_email} *</label>
    <input name='email' value='{$email}' type='email' required />
  </p>
  <p {if isset($error_password)}class="error"{/if}>
    <label for='password'>{$lang_password} *</label>
    <input name='password' type='password' required />
  </p>
  <p>
    <label for='password2'>{$lang_password_repeat} *</label>
    <input name='password2' type='password' required />
    <img id="js-icon" src='%PATH_IMAGES%/spacer.png' class="icon-close" alt="" />
  </p>
  {if $USER_RIGHT < 4}
    <p {if isset($error_email)}class="error"{/if}>
      <label>
        <a href='/help/Registration' id="js-fancybox">
          {$lang_disclaimer_read} *
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
<script language='javascript' src='%PATH_PUBLIC%/js/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script language='javascript' type="text/javascript">
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