<h1>
  {$lang.global.registration}
  {if !$USER_FACEBOOK_ID && !$USER_NAME && $_facebook_plugin_ == true}
    <fb:login-button scope="email" onlogin="window.location='{$CURRENT_URL}"></fb:login-button>
  {/if}
</h1>
<form method='post'>
  <fieldset>
    <div class="clearfix{if isset($error.name)} alert-message block-message error{/if}">
      <label for='input-name'>{$lang.global.name} <span title="{$lang.global.required}">*</span></label>
      <div class="input">
        <input name='name' value='{$name}' type='name' id="input-name" autofocus required />
        <span class="help-inline">{if isset($error.name)}{$error.name}{/if}</span>
      </div>
    </div>
    <div class="clearfix">
      <label for='input-surname'>{$lang.global.surname} <span title="{$lang.global.required}">*</span></label>
      <div class="input">
        <input name='surname' value='{$surname}' id="input-surname" type='text' />
      </div>
    </div>
    <div class="clearfix {if isset($error.email)} error{/if}">
      <label for='input-email'>{$lang.global.email.email} <span title="{$lang.global.required}">*</span></label>
      <div class="input">
        <input name='email' value='{$email}' type='email' id="input-email" required />
        <span class="help-inline">{if isset($error.email)}{$error.email}{/if}</span>
      </div>
    </div>
    <div class="clearfix{if isset($error.password)} error{/if}">
      <label for='input-password'>{$lang.global.password.password} <span title="{$lang.global.required}">*</span></label>
      <div class="input">
        <input name='password' type='password' id="input-password" required />
        <span class="help-inline">{if isset($error.password)}{$error.password}{/if}</span>
      </div>
    </div>
    <div class="clearfix">
      <label for='input-password2'>{$lang.global.password.repeat} <span title="{$lang.global.required}">*</span></label>
      <div class="input">
        <input name='password2' type='password' id="input-password2" required />
        <img id="js-icon" src='%PATH_IMAGES%/spacer.png' class="icon-close" alt="" width="16" height="16" />
      </div>
    </div>
    {if $USER_ROLE < 4}
      <div class="clearfix{if isset($error.disclaimer)} error{/if}">
        <label>
          {* Absolute URL due to fancybox bug *}
          <a href='{$WEBSITE_URL}/content/2/ajax' id="js-fancybox" class='fancybox.ajax'>
            {$lang.global.terms.read} <span title="{$lang.global.required}">*</span>
          </a>
        </label>
        <div class="input">
          <input name='disclaimer' value='' type='checkbox' required />
        </div>
      </div>
    {/if}
    <div class="actions">
      <input type='submit' class="btn primary" value='{$lang.global.register}' />
      <input type='hidden' value='formdata' name='create_user' />
    </div>
  </fieldset>
</form>
<script src='%PATH_JS%/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script type="text/javascript">
  $(document).ready(function(){
    $("#js-fancybox").fancybox();

    $("input[name='password2']").keyup(function(){
      if ($("input[name='password']").val() == $("input[name='password2']").val()){
        $('#js-icon').attr('class', 'icon-success');
      } else {
        $('#js-icon').attr('class', 'icon-close');
      }
    });
  });
</script>