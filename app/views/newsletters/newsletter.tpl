<form method='post'>
  <h1>{$lang.newsletter.title.handle}</h1>
  <h4>
    {$lang.newsletter.info.handle}
  </h4>
  <p {if isset($error_email)}class="error" title="{$error_email}"{/if}>
    <label for="input-email">{$lang.global.email.email} <span title="{$lang.global.required}">*</span></label>
    <input type="email" name='email' id="input-email" autofocus required />
  </p>
  <p class="center">
    <input type='submit' value='{$lang.newsletter.title.handle}' />
  </p>
</form>