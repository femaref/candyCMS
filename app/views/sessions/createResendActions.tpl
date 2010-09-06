<div id="resend_actions">
  <form method='post' action='{$_action_url_}'>
    {if $error_email}
      <div class="error">{$error_email}</div>
    {/if}
    <fieldset>
      <legend>{$lang_headline}</legend>
      <p class='small'>{$lang_description}</p>
      <div class="input">
        <input name='email' title='' />
      </div>
      <div class="submit">
        <input type='submit' value='{$lang_submit}' />
      </div>
    </fieldset>
  </form>
</div>