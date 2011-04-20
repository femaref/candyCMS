<a name='create'></a>
<form action="{$_action_url_}" method="post">
  <h3>{$lang_headline}{if !$USER_FACEBOOK_ID && !$USER_NAME} <fb:login-button perms="email" onlogin="window.location='{$CURRENT_URL}#comments'"></fb:login-button>{/if}</h3>
  <p {if $error_name}class="error"{/if}>
    <label for="name">{$lang_name} *</label>
    {if $USER_NAME}
      {$USER_FULL_NAME}
      {if $USER_FACEBOOK_ID}
        <input type="hidden" value="{$USER_FULL_NAME}" name="name" />
        <input type="hidden" value="{$USER_FACEBOOK_ID}" name="facebook_id" />
      {/if}
    {else}
      <input type="text" value="{$name}" name="name" required />
      {if $error_name}
        <div class="description">{$error_name}</div>
      {/if}
    {/if}
  </p>
  <p>
    <label for="name">{$lang_email}</label>
    {if $USER_EMAIL}
      {$USER_EMAIL}
      {if $USER_FACEBOOK_ID}
        <input type="hidden" value="{$USER_EMAIL}" name="email" />
      {/if}
    {else}
      <input type="email" value="{$email}" name="email" title="{$lang_email_info}" />
    {/if}
  </p>
  <p {if $error_content}class="error"{/if}>
    <label for='js-create_commment_text'>{$lang_content} *</label>
    <textarea name='content' id='js-create_commment_text' rows='10' cols='50' required>{$content}</textarea>
  </p>
  {if $_captcha_}
    <script type="text/javascript">var RecaptchaOptions = { lang:'de',theme:'white' };</script>
    {$_captcha_}
  {/if}
  <p class="center">
    <input type='submit' value='{$lang_submit}' />
    <input type='reset' value='{$lang_reset}' />
    <input type='hidden' value='formdata' name='create_comment' />
    <input type='hidden' value='{$_parent_id_}' name='parent_id' />
  </p>
</form>
<script language='javascript' src='%PATH_PUBLIC%/js/core/jquery.tipTip{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script language='javascript' type="text/javascript">
  $(document).ready(function(){
    $('#email').tipTip();
  });
</script>
