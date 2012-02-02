<a name='create'></a>
<form method="post" data-ajax="false">
  {if !$USER_FACEBOOK_ID && !$USER_NAME && $_facebook_plugin_ == true}
    <p>
      <fb:login-button scope="email" onlogin="window.location='{$CURRENT_URL}#comments'"></fb:login-button>
    </p>
  {/if}
  <div class="clearfix{if isset($error.name)} error{/if}">
    <label for="input-name">{$lang.global.name} <span title="{$lang.global.required}">*</span></label>
    <div class="input">
      {if $USER_NAME}
        <input type="text" name="name" value="{$USER_FULL_NAME}" disabled class="xlarge"  />
        {if $USER_FACEBOOK_ID}
          <input type="hidden" value="{$USER_FULL_NAME}" name="name" id="input-name" />
          <input type="hidden" value="{$USER_FACEBOOK_ID}" name="facebook_id" />
        {/if}
      {else}
        <input type="text" value="{if isset($name)}{$name}{/if}" name="name" id="input-name" required class="xlarge" />
        {if isset($error.name)}
          <div class="description">{$error.name}</div>
        {/if}
      {/if}
    </div>
  </div>
  <div class="clearfix">
    <label for="input-email">{$lang.global.email.email}</label>
    <div class="input">
      {if $USER_EMAIL}
        <input type="text" name="email" value="{$USER_EMAIL}" disabled class="xlarge" />
        {if $USER_FACEBOOK_ID}
          <input type="hidden" value="{$USER_EMAIL}" name="email" id="input-email" />
        {/if}
      {else}
        <input type="email" value="{if isset($email)}{$email}{/if}" name="email" id="input-email" class="xlarge" />
      {/if}
    </div>
  </div>
  <div class="clearfix{if isset($error.content)} error{/if}">
    <label for='js-create_commment_text'>{$lang.global.content} <span title="{$lang.global.required}">*</span></label>
    <div class="input">
      <textarea name='content' id='js-create_commment_text' class="xlarge"  required>{if isset($content)}{$content}{/if}</textarea>
    </div>
  </div>
  {if isset($_captcha_) && $MOBILE == false}
    {include file='../layouts/_recaptcha.tpl'}
  {/if}
  <div class="actions">
    <input type='submit' value='{$lang.comment.title.create}' data-theme="b" class="btn primary"/>
    <input type='hidden' value='formdata' name='create_comment' />
    <input type='hidden' value='{$_request_id_}' name='parent_id' />
    <input type='reset' value='{$lang.global.reset}' class="btn"/>
  </div>
</form>
<script type='text/javascript' src='%PATH_JS%/core/jquery.tiptip{$_compress_files_suffix_}.js'></script>
<script type="text/javascript">
  $('p.error').tipTip({ maxWidth: "auto" });
</script>