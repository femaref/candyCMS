<form method='post'>
  <h1>{$lang.global.search}</h1>
  <div class='clearfix{if isset($error.search)} error{/if}'>
    <label for='input-search'>{$lang.search.label.terms} <span title="{$lang.global.required}">*</span></label>
    <div class="input">
      <input type="search" name="search" id="input-search" autofocus required />
    </div>
  </div>
  <div class="actions">
    <input type="submit" class='btn primary' value="{$lang.global.search}" data-theme="b" />
  </div>
</form>
<script type='text/javascript' src='%PATH_JS%/core/jquery.tiptip{$_compress_files_suffix_}.js'></script>
<script type="text/javascript">
  $('p.error').tipTip({ maxWidth: "auto" });
</script>