<form method='post'>
  <h1>{$lang.global.login}</h1>
  <p {if isset($error.email)}class="error" title="{$error.email}"{/if}>
    <label for="input-email">{$lang.global.email.email}<span title="{$lang.global.required}">*</span></label>
    <input name='email' type='email' value="{$email}" id="input-email" autofocus required />
  </p>
  <p {if isset($error.password)}class="error" title="{$error.password}"{/if}>
    <label for='input-password'>{$lang.global.password.password}<span title="{$lang.global.required}">*</span></label>
    <input name='password' type='password' id="input-password" required />
  </p>
  <p class="center">
    <a href='/session/resendpassword'>{$lang.session.password.title}</a>
    |
    <a href='/session/resendverification'>{$lang.session.verification.title}</a>
  </p>
  <p class="center">
    <input type='submit' value='{$lang.global.login}' />
    <input type='hidden' value='formdata' name='create_session' />
  </p>
</form>
<script type='text/javascript' src='%PATH_JS%/core/jquery.tiptip{$_compress_files_suffix_}.js'></script>
<script type="text/javascript">
  $('p.error').tipTip({ maxWidth: "auto" });
</script>