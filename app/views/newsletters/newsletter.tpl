<form method='post' action='/newsletter'>
  <h1>{$lang_headline}</h1>
  <h4>
    {$lang_description}
  </h4>
  <p {if $error_email}class="error"{/if}>
    <label for="email">{$lang_email} *</label>
    <input type="email" name='email' title='{$lang_email}' autofocus />
  </p>
  <p class="center">
    <input type='submit' value='{$lang_headline}' />
  </p>
</form>