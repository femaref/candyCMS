<div id="login">
  <form method='post' action='/Session/create'>
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
    <fieldset>
      <legend>{$lang_login}</legend>
      <div class="input">
        <label for='email'>{$lang_email}:</label>
        &nbsp;
        <input name='email' type='text' id='email' value='' />
      </div>
      &nbsp;
      <div class="input">
        <label for='password'>{$lang_password}:</label>
        &nbsp;
        <input name='password' type='password' id='password' value='' />
      </div>
      <div class="submit">
        <input type='submit' value='{$lang_login}' />
      </div>
    </fieldset>
    <input type='hidden' value='formdata' name='create_session' />
    <p>
      <a href='/session/resendpassword'>{$lang_lost_password}</a>
      |
      <a href='/session/resendverification'>{$lang_resend_verification}</a>
    </p>
  </form>
</div>