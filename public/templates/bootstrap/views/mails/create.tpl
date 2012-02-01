<h1>{$lang.global.contact} {$contact.name} {$contact.surname}</h1>
<form method='post' action='/mail/{$_request_id_}' id="create_mail">
  <fieldset>
    <div class='clearfix{if isset($error.email)} error{/if}'>
      <label for='email'>{$lang.global.email.email} <span title="{$lang.global.required}">*</span></label>
      <div class='input'>
        {if $email == ''}
          <input name='email' value='{$email}' type='email' required />
          {if isset($error.email)}
            <span class="description">{$error.email}</span>
          {/if}
        {else}
          <input name='email' value='{$email}' type='email' required />
        {/if}
        {if isset($error.email)}<span class='help-inline'>{$error.email}</span>{/if}
      </div>
    </div>
    <div class='clearfix'>
      <label for='subject'>{$lang.global.subject}</label>
      <div class='input'>
        <input name='subject' class='' value='{$subject}' type='text' />
      </div>
    </div>
    <div class='clearfix{if isset($error.content)} error{/if}'>
      <label for='content'>{$lang.global.content} <span title="{$lang.global.required}">*</span></label>
      <div class='input'>
        <textarea name='content' cols="30" required>{$content}</textarea>
      </div>
    </div>
    {if isset($_captcha_) && $MOBILE == false}
      {include file='../layouts/_recaptcha.tpl'}
    {/if}
    <div class="actions">
      <input type='submit' class='btn primary' value='{$lang.global.submit}' />
      <input type='hidden' value='formdata' name='create_mail' />
    </div>
  </fieldset>
</form>
<script type='text/javascript' src='%PATH_JS%/core/jquery.tiptip{$_compress_files_suffix_}.js'></script>
<script type="text/javascript">
  $('p.error').tipTip({ maxWidth: "auto" });
</script>