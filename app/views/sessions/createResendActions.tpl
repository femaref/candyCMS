<form method='post' action='{$_action_url_}'>
  <h1>{$lang_headline}</h1>
  <h4>{$lang_description}</h4>
  <p>
    <label for="email">{$lang_email} <span title="{$lang_required}">*</span></label>
    <input name='email' type="email" title='' autofocus required />
  </p>
  <p class="center">
    <input type='submit' value='{$lang_submit}' />
  </p>
</form>