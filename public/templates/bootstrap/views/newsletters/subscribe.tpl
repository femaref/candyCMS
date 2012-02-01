<h1>{$lang.newsletter.title.subscribe}</h1>
<p>
  {$lang.newsletter.info.subscribe}
</p>
<form method='post'>
  <fieldset>
    <div class='clearfix'>
      <label for='input-name'>{$lang.global.name}</label>
      <div class='input'>
        <input name='name' type='name' id='input-name' autofocus />
      </div>
    </div>
    <div class='clearfix'>
      <label for='input-surname'>{$lang.global.surname}</label>
      <div class='input'>
        <input name='surname' id='input-surname' type='text' />
      </div>
    </div>
    <div class='clearfix{if isset($error.email)} error{/if}'>
      <label for='input-email'>{$lang.global.email.email} <span title='{$lang.global.required}'>*</span></label>
      <div class='input'>
        <input type='email' name='email' id='input-email' autofocus required />
      </div>
    </div>
    <div class='actions'>
      <input type='submit' class='btn primary' value='{$lang.newsletter.title.subscribe}' />
    </div>
  </fieldset>
</form>
<script type='text/javascript' src='%PATH_JS%/core/jquery.tiptip{$_compress_files_suffix_}.js'></script>
<script type='text/javascript'>
  $('p.error').tipTip({ maxWidth: 'auto' });
</script>