<form method='post'>
  <h1>{$lang_login}</h1>
  <p {if isset($error_email)}class="error" title="{$error_email}"{/if}>
    <label for="email">{$lang_email} *</label>
    <input name='email' type='email' autofocus required />
  </p>
  <p {if isset($error_password)}class="error" title="{$error_password}"{/if}>
    <label for='password'>{$lang_password} *</label>
    <input name='password' type='password' required />
  </p>
  <p class="center">
    <a href='/session/resendpassword'>{$lang_lost_password}</a>
    |
    <a href='/session/resendverification'>{$lang_resend_verification}</a>
  </p>
  <p class="center">
    <input type='submit' value='{$lang_login}' />
    <input type='hidden' value='formdata' name='create_session' />
  </p>
</form>