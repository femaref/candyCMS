<a name='create'></a>
<form method="post" data-ajax="false">
  <h3>{$lang.comment.title.create}{if !$USER_FACEBOOK_ID && !$USER_NAME && $_facebook_plugin_ == true} <fb:login-button perms="email" onlogin="window.location='{$CURRENT_URL}#comments'"></fb:login-button>{/if}</h3>
  <p {if isset($error.name)}class="error" title="{$error.name}"{/if}>
    <label for="input-name">{$lang.global.name} <span title="{$lang.global.required}">*</span></label>
    {if $USER_NAME}
      <input type="text" name="name" value="{$USER_FULL_NAME}" disabled />
      {if $USER_FACEBOOK_ID}
        <input type="hidden" value="{$USER_FULL_NAME}" name="name" id="input-name" />
        <input type="hidden" value="{$USER_FACEBOOK_ID}" name="facebook_id" />
      {/if}
    {else}
      <input type="text" value="{if isset($name)}{$name}{/if}" name="name" id="input-name" required />
      {if isset($error.name)}
        <div class="description">{$error.name}</div>
      {/if}
    {/if}
  </p>
  <p>
    <label for="input-email">{$lang.global.email.email}</label>
    {if $USER_EMAIL}
      <input type="text" name="email" value="{$USER_EMAIL}" disabled />
      {if $USER_FACEBOOK_ID}
        <input type="hidden" value="{$USER_EMAIL}" name="email" id="input-email" />
      {/if}
    {else}
      <input type="email" value="{if isset($email)}{$email}{/if}" name="email" id="input-email" />
    {/if}
  </p>
  <p {if isset($error.content)}class="error" title="{$error.content}"{/if}>
    <label for='js-create_commment_text'>{$lang.global.content} <span title="{$lang.global.required}">*</span></label>
    <textarea name='content' id='js-create_commment_text' rows='10' cols='50' required>{if isset($content)}{$content}{/if}</textarea>
  </p>
  {if isset($_captcha_) && $MOBILE === false}
    <div {if isset($error.captcha)}class="error" title="{$error.captcha}"{/if}>
      <script type="text/javascript">var RecaptchaOptions = { lang:'{$WEBSITE_LANGUAGE}',theme:'clean' };</script>
      {$_captcha_}
    </div>
  {/if}
  <p class="center">
    <input type='submit' value='{$lang.comment.title.create}' data-theme="b" />
    <input type='hidden' value='formdata' name='create_comment' />
    <input type='hidden' value='{$_request_id_}' name='parent_id' />
    <input type='reset' value='{$lang.global.reset}' />
  </p>
</form>
<script type='text/javascript' src='%PATH_JS%/core/jquery.tiptip{$_compress_files_suffix_}.js'></script>
<script type="text/javascript">
  $('p.error').tipTip({ maxWidth: "auto" });
</script>