<form method='post' action='/session/{$smarty.get.action}'>
  <h1>{if $smarty.get.action == 'verification'}{$lang.session.verification.title}{else}{$lang.session.password.title}{/if}</h1>
  <h4>{if $smarty.get.action == 'verification'}{$lang.session.verification.info}{else}{$lang.session.password.info}{/if}</h4>
  <p {if isset($error.email)}class="error" title="{$error.email}"{/if}>
    <label for="input-email">{$lang.global.email.email} <span title="{$lang.global.required}">*</span></label>
    <input name='email' type="email" title='' id="input-email" autofocus required />
  </p>
  <p class="center">
    <input type='submit' value='{$lang.global.submit}' data-theme="b" />
  </p>
</form>
<script type='text/javascript' src='%PATH_JS%/core/jquery.tiptip{$_compress_files_suffix_}.js'></script>
<script type="text/javascript">
  $('p.error').tipTip({ maxWidth: "auto" });
</script>