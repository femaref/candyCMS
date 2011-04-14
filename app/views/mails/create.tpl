<form method='post' action='/mail/{$_request_id_}'>
  <h1>{$lang_headline} {$contact.name} {$contact.surname}</h1>
  <p {if $error_email}class="error"{/if}>
    <label for='email'>{$lang_email} *</label>
    {if $email == ''}
      <input name='email' id='email' value='{$email}' type='email' required />
      {if $error_email}
        <div class="description">{$error_email}</div>
      {/if}
    {else}
      <input name='email' id='email' value='{$email}' type='hidden' disabled />
    {/if}
  </p>
  <p>
    <label for='subject'>{$lang_subject}</label>
    <input name='subject' class='' id='subject' value='{$subject}' type='text' />
  </p>
  <p {if $error_content}class="error"{/if}>
    <label for='content'>{$lang_content} *</label>
    <textarea name='content' id='content' required>{$content}</textarea>
  </p>
  {if $_captcha_}
    <script type="text/javascript">var RecaptchaOptions = { lang:'de',theme:'white' };</script>
    {$_captcha_}
  {/if}
  <p class="center">
    <input type='submit' value='{$lang_submit}' />
    <input type='hidden' value='formdata' name='send_mail' />
  </p>
</form>