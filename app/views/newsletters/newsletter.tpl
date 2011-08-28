<form method='post'>
  <h1>{$lang_headline}</h1>
  <h4>
    {$lang_description}
  </h4>
  <p {if isset($error_email)}class="error" title="{$error_email}"{/if}>
    <label for="input-email">{$lang_email} *</label>
    <input type="email" name='email' id="input-email" title='{$lang_email}' autofocus required />
  </p>
  <p class="center">
    <input type='submit' value='{$lang_headline}' />
  </p>
</form>