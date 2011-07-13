<form method='post' action='/mail/{$_request_id_}' id="create_mail">
  <h1>{$lang_headline} {$contact.name} {$contact.surname}</h1>
  <p {if isset($error_email)}class="error"{/if}>
    <label for='email'>{$lang_email} *</label>
    {if $email == ''}
      <input name='email' value='{$email}' type='email' required />
      {if isset($error_email)}
        <div class="description">{$error_email}</div>
      {/if}
    {else}
      <input name='email' value='{$email}' type='email' required />
    {/if}
  </p>
  <p>
    <label for='subject'>{$lang_subject}</label>
    <input name='subject' class='' value='{$subject}' type='text' />
  </p>
  <p {if isset($error_content)}class="error"{/if}>
    <label for='content'>{$lang_content} *</label>
    <textarea name='content' cols="30" required>{$content}</textarea>
  </p>
  {if isset($_captcha_)}
    <div {if isset($error_captcha)}class="error"{/if}>
      <script type="text/javascript">var RecaptchaOptions = { lang:'de',theme:'white' };</script>
      {$_captcha_}
    </div>
  {/if}
  <p class="center">
    <input type='submit' value='{$lang_submit}' />
    <input type='hidden' value='formdata' name='send_mail' />
  </p>
</form>