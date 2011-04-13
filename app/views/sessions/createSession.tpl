<form method='post' action='/session/create'>
  <h1>{$lang_login}</h1>
  {if $error_email || $error_password}
    <div class="error">
      {if $error_email}
        <p>{$error_email}</p>
      {/if}
      {if $error_password}
        <p>{$error_password}</p>
      {/if}
    </div>
  {/if}
  <p>
    <label for="name">{$lang_email}</label>
    <input name='email' type='text' id='email' value='' autofocus required />
  </p>
  <p>
    <label for='password'>{$lang_password}</label>
    <input name='password' type='password' id='password' value='' required />
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
