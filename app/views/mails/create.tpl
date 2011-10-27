<form method='post' action='/mail/{$_request_id_}' id="create_mail">
  <h1>{$lang.global.contact} {$contact.name} {$contact.surname}</h1>
  <p {if isset($error_email)}class="error" title="{$error_email}"{/if}>
    <label for='email'>{$lang.global.email.email}<span title="{$lang.global.required}">*</span></label>
    {if $email == ''}
      <input name='email' value='{$email}' type='email' required />
      {if isset($error_email)}
        <span class="description">{$error_email}</span>
      {/if}
    {else}
      <input name='email' value='{$email}' type='email' required />
    {/if}
  </p>
  <p>
    <label for='subject'>{$lang.global.subject}</label>
    <input name='subject' class='' value='{$subject}' type='text' />
  </p>
  <p {if isset($error_content)}class="error" title="{$error_content}"{/if}>
    <label for='content'>{$lang.global.content}<span title="{$lang.global.required}">*</span></label>
    <textarea name='content' cols="30" required>{$content}</textarea>
  </p>
  {if isset($_captcha_) && $MOBILE === false}
    <div {if isset($error_captcha)}class="error" title="{$error_captcha}"{/if}>
      <script type="text/javascript">var RecaptchaOptions = { lang:'{$WEBSITE_LANGUAGE}',theme:'white' };</script>
      {$_captcha_}
    </div>
  {/if}
  <p class="center">
    <input type='submit' value='{$lang.global.submit}' />
    <input type='hidden' value='formdata' name='create_mail' />
  </p>
</form>