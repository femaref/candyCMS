<form method='post' action='/session/{$smarty.get.action}'>
  <h1>{if $smarty.get.action == 'resendverification'}{$lang.session.verification.title}{else}{$lang.session.password.title}{/if}</h1>
  <h4>{if $smarty.get.action == 'resendverification'}{$lang.session.verification.info}{else}{$lang.session.password.info}{/if}</h4>
  <p {if isset($error_email)}class="error" title="{$error_email}"{/if}>
    <label for="input-email">{$lang.global.email.email} <span title="{$lang.global.required}">*</span></label>
    <input name='email' type="email" title='' id="input-email" autofocus required />
  </p>
  <p class="center">
    <input type='submit' value='{$lang.global.submit}' />
  </p>
</form>