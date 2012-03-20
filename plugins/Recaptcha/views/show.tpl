{strip}
  {if isset($_captcha_) && $MOBILE === false}
    <div class="control-group{if isset($_error_)} alert alert-error{/if}">
      <label class='control-label'>
        {$lang.global.captcha} <span title="{$lang.global.required}">*</span>
      </label>
      <div class="controls">
        <script type="text/javascript">var RecaptchaOptions = { lang:'{$WEBSITE_LANGUAGE}',theme:'white' };</script>
        {$_captcha_}
        {if isset($_error_)}
          <span class='help-inline'>
            {$_error_}
          </span>
        {/if}
      </div>
    </div>
  {/if}
{/strip}