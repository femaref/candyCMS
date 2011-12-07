<div {if isset($error.captcha)}class="error" title="{$error.captcha}"{/if}>
  <script type="text/javascript">var RecaptchaOptions = { lang:'{$WEBSITE_LANGUAGE}',theme:'white' };</script>
  {$_captcha_}
</div>