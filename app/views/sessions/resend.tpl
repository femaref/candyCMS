<form method='post' action='/session/{$smarty.get.action}'>
  <h1>{$lang.session.headline}</h1>
  <h4>{$lang_description}</h4>
  <p {if isset($error_email)}class="error" title="{$error_email}"{/if}>
    <label for="input-email">{$lang_email} <span title="{$lang_required}">*</span></label>
    <input name='email' type="email" title='' id="input-email" autofocus required />
  </p>
  <p class="center">
    <input type='submit' value='{$lang.global.create}' />
  </p>
</form>