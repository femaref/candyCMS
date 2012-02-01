<div class="clearfix{if isset($error.captcha)} error{/if}">
  <label></label>
  <div class="input">
    <script type="text/javascript">var RecaptchaOptions = { lang:'{$WEBSITE_LANGUAGE}',theme:'white' };</script>
    {$_captcha_}
  </div>
</div>