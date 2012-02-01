<form method='post' action='/session/{$smarty.get.action}'>
  <h1>{if $smarty.get.action == 'verification'}{$lang.session.verification.title}{else}{$lang.session.password.title}{/if}</h1>
  <p>{if $smarty.get.action == 'verification'}{$lang.session.verification.info}{else}{$lang.session.password.info}{/if}</p>
  <div class="clearfix{if isset($error.email)} error{/if}">
    <label for="input-email">{$lang.global.email.email} <span title="{$lang.global.required}">*</span></label>
    <div class="input">
      <input name='email' type="email" title='' id="input-email" autofocus required />
    </div>
  </div>
  <div class="actions">
    <input type='submit' class='btn primary' value='{$lang.global.submit}' data-theme="b" />
  </div>
</form>
<script type='text/javascript' src='%PATH_JS%/core/jquery.tiptip{$_compress_files_suffix_}.js'></script>
<script type="text/javascript">
  $('p.error').tipTip({ maxWidth: "auto" });
</script>