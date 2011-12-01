<form method='post'>
  <h1>{$lang.newsletter.title.subscribe}</h1>
  <h4>
    {$lang.newsletter.info.subscribe}
  </h4>
  <p {if isset($error.email)}class="error" title="{$error.email}"{/if}>
    <label for="input-email">{$lang.global.email.email} <span title="{$lang.global.required}">*</span></label>
    <input type="email" name='email' id="input-email" autofocus required />
  </p>
  <p class="center">
    <input type='submit' value='{$lang.newsletter.title.subscribe}' />
  </p>
</form>
<script type='text/javascript' src='%PATH_JS%/core/jquery.tiptip{$_compress_files_suffix_}.js'></script>
<script type="text/javascript">
  $('p.error').tipTip({ maxWidth: "auto" });
</script>