<form method='post'>
  <h1>
    {$lang.global.registration}
    {if !$USER_FACEBOOK_ID && !$USER_NAME && $_facebook_plugin_ == true}
      <fb:login-button scope="email" onlogin="window.location='{$CURRENT_URL}"></fb:login-button>
    {/if}
  </h1>
  <p {if isset($error.name)}class="error" title="{$error.name}"{/if}>
    <label for='input-name'>{$lang.global.name} <span title="{$lang.global.required}">*</span></label>
    <input name='name' value='{$name}' type='name' id="input-name" autofocus required />
  </p>
  <p>
    <label for='input-surname'>{$lang.global.surname} <span title="{$lang.global.required}">*</span></label>
    <input name='surname' value='{$surname}' id="input-surname" type='text' />
  </p>
  <p {if isset($error.email)}class="error" title="{$error.email}"{/if}>
    <label for='input-email'>{$lang.global.email.email} <span title="{$lang.global.required}">*</span></label>
    <input name='email' value='{$email}' type='email' id="input-email" required />
  </p>
  <p {if isset($error.password)}class="error" title="{$error.password}"{/if}>
    <label for='input-password'>{$lang.global.password.password} <span title="{$lang.global.required}">*</span></label>
    <input name='password' type='password' id="input-password" required />
  </p>
  <p>
    <label for='input-password2'>{$lang.global.password.repeat} <span title="{$lang.global.required}">*</span></label>
    <input name='password2' type='password' id="input-password2" required />
    <img id="js-icon" src='%PATH_IMAGES%/spacer.png' class="icon-close" alt="" width="16" height="16" />
  </p>
  {if $USER_ROLE < 4}
    <p {if isset($error.disclaimer)}class="error" title="{$error.disclaimer}"{/if}>
      <label>
        {* Absolute URL due to fancybox bug *}
        <a href='{$WEBSITE_URL}/content/2/ajax' id="js-fancybox" class='fancybox.ajax'>
          {$lang.global.terms.read} <span title="{$lang.global.required}">*</span>
        </a>
      </label>
      <input name='disclaimer' value='' type='checkbox' required />
    </p>
  {/if}
  <p class="center">
    <input type='submit' value='{$lang.global.register}' />
    <input type='hidden' value='formdata' name='create_user' />
  </p>
</form>
<script src='%PATH_JS%/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script type='text/javascript' src='%PATH_JS%/core/jquery.tiptip{$_compress_files_suffix_}.js'></script>
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

  $('p.error').tipTip({ maxWidth: "auto" });
</script>