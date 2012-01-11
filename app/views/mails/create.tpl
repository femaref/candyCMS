<form method='post' action='/mail/{$_request_id_}' id="create_mail">
  <h1>{$lang.global.contact} {$contact.name} {$contact.surname}</h1>
  <p {if isset($error.email)}class="error" title="{$error.email}"{/if}>
    <label for='email'>{$lang.global.email.email} <span title="{$lang.global.required}">*</span></label>
    {if $email == ''}
      <input name='email' value='{$email}' type='email' required />
      {if isset($error.email)}
        <span class="description">{$error.email}</span>
      {/if}
    {else}
      <input name='email' value='{$email}' type='email' required />
    {/if}
  </p>
  <p>
    <label for='subject'>{$lang.global.subject}</label>
    <input name='subject' class='' value='{$subject}' type='text' />
  </p>
  <p {if isset($error.content)}class="error" title="{$error.content}"{/if}>
    <label for='content'>{$lang.global.content} <span title="{$lang.global.required}">*</span></label>
    <textarea name='content' cols="30" required>{$content}</textarea>
  </p>
  {if isset($_captcha_) && $MOBILE == false}
    {include file='../layouts/_recaptcha.tpl'}
  {/if}
  <p class="center">
    <input type='submit' value='{$lang.global.submit}' />
    <input type='hidden' value='formdata' name='create_mail' />
  </p>
</form>
<script type='text/javascript' src='%PATH_JS%/core/jquery.tiptip{$_compress_files_suffix_}.js'></script>
<script type="text/javascript">
  $('p.error').tipTip({ maxWidth: "auto" });
</script>