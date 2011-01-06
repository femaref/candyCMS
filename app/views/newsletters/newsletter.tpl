<div id="newsletter">
  <form method='post' action='/newsletter'>
    {if $error_email}
      <div class="error">{$error_email}</div>
    {/if}
    <fieldset>
      <legend>{$lang_headline}</legend>
      <p class='small'>{$lang_description}</p>
      <div class="input">
        <input name='email' title='{$lang_email}' />
      </div>
      <div class="submit">
        <input type='submit' value='{$lang_headline}' />
      </div>
    </fieldset>
  </form>
</div>