<a name='create'></a>
<form method="post">
  <h3>{$lang_headline}{if !$USER_FACEBOOK_ID && !$USER_NAME && $_facebook_plugin_ == true} <fb:login-button perms="email" onlogin="window.location='{$CURRENT_URL}#comments'"></fb:login-button>{/if}</h3>
  <p {if isset($error_name)}class="error" title="{$error_name}"{/if}>
    <label for="input-name">{$lang_name} <span title="{$lang_required}">*</span></label>
    {if $USER_NAME}
      {$USER_FULL_NAME}
      {if $USER_FACEBOOK_ID}
        <input type="hidden" value="{$USER_FULL_NAME}" name="name" id="input-name" />
        <input type="hidden" value="{$USER_FACEBOOK_ID}" name="facebook_id" />
      {/if}
    {else}
      <input type="text" value="{$name}" name="name" id="input-name" required />
      {if isset($error_name)}
        <div class="description">{$error_name}</div>
      {/if}
    {/if}
  </p>
  <p>
    <label for="input-email">{$lang_email}</label>
    {if $USER_EMAIL}
      {$USER_EMAIL}
      {if $USER_FACEBOOK_ID}
        <input type="hidden" value="{$USER_EMAIL}" name="email" id="input-email" />
      {/if}
    {else}
      <input type="email" value="{$email}" name="email" />
    {/if}
  </p>
  <p {if isset($error_content)}class="error" title="{$error_content}"{/if}>
    <label for='js-create_commment_text'>{$lang_content} <span title="{$lang_required}">*</span></label>
    <textarea name='content' id='js-create_commment_text' rows='10' cols='50' required>{$content}</textarea>
  </p>
  {if isset($_captcha_)}
    <div {if isset($error_captcha)}class="error" title="{$error_captcha}"{/if}>
      <script type="text/javascript">var RecaptchaOptions = { lang:'{$WEBSITE_LANGUAGE}',theme:'white' };</script>
      {$_captcha_}
    </div>
  {/if}
  <p class="center">
    <input type='submit' value='{$lang_submit}' />
    <input type='hidden' value='formdata' name='create_comment' />
    <input type='hidden' value='{$_parent_id_}' name='parent_id' />
    <input type='reset' value='{$lang_reset}' />
  </p>
</form>
