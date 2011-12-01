<form method='post'>
  <h1>{$lang.global.search}</h1>
  <p {if isset($error.search)}class="error" title="{$error.search}"{/if}>
    <label for='input-id'>{$lang.search.label.terms}<span title="{$lang.global.required}">*</span></label>
    <input type="search" name="id" id="input-id" autofocus required />
  </p>
  <p class="center">
    <input type="submit" value="{$lang.global.search}" />
  </p>
</form>
<script type='text/javascript' src='%PATH_JS%/core/jquery.tiptip{$_compress_files_suffix_}.js'></script>
<script type="text/javascript">
  $('p.error').tipTip({ maxWidth: "auto" });
</script>