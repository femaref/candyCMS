<div id="resend_actions">
  <form method='post' action='{$_action_url_}'>
    {if $error_email}
      <div class="error">{$error_email}</div>
    {/if}
    <fieldset>
      <legend>{$lang_headline}</legend>
      <small>{$lang_description}</small>
      <div class="input">
        <input name='email' type="email" title='' autofocus required />
      </div>
      <div class="submit">
        <input type='submit' value='{$lang_submit}' />
      </div>
    </fieldset>
  </form>
</div>