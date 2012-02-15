{strip}
  {if isset($_captcha_) && $MOBILE == false && $AJAX_REQUEST == false}
    <div class="control-group{if isset($error.captcha)} error{/if}">
      <label class='control-label'>
        {$lang.global.captcha} <span title="{$lang.global.required}">*</span>
      </label>
      <div class="controls">
        <script type="text/javascript">var RecaptchaOptions = { lang:'{$WEBSITE_LANGUAGE}',theme:'white' };</script>
        {$_captcha_}
      </div>
    </div>
  {/if}
{/strip}