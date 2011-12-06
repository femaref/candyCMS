<div {if isset($error.captcha)}class="error" title="{$error.captcha}"{/if}>
  <script type="text/javascript">var RecaptchaOptions = { lang:'{$WEBSITE_LANGUAGE}',theme:'clean' };</script>
  {$_captcha_}
</div>