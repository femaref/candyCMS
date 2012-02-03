<form method='post' data-ajax="false">
  <h1>{$lang.global.login}</h1>
  <div class="clearfix{if isset($error.email)} error" title="{$error.email}{/if}">
    <label for="input-email">{$lang.global.email.email} <span title="{$lang.global.required}">*</span></label>
    <div class="input">
      <input name='email' type='email' value="{$email}" id="input-email" autofocus required />
    </div>
  </div>
  <div class="clearfix{if isset($error.password)} error" title="{$error.password}{/if}">
    <label for='input-password'>{$lang.global.password.password} <span title="{$lang.global.required}">*</span></label>
    <div class="input">
      <input name='password' type='password' id="input-password" required />
    </div>
  </div>
  <div class="actions">
    <input type='submit' value='{$lang.global.login}' data-theme="b" class="btn primary"/>
    <a href='/session/password' class="btn">{$lang.session.password.title}</a>
    <a href='/session/verification' class="btn">{$lang.session.verification.title}</a>
    <input type='hidden' value='formdata' name='create_session' />
  </div>
</form>
<script type='text/javascript' src='%PATH_JS%/core/jquery.tiptip{$_compress_files_suffix_}.js'></script>
<script type="text/javascript">
  $('p.error').tipTip({ maxWidth: "auto" });
</script>