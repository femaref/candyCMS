<form action='/media/create' method='post' enctype='multipart/form-data'>
  <h1>{$lang.media.title.create}</h1>
  <div class='clearfix'>
    <label for='input-file'>{$lang.media.label.choose} <span title="{$lang.global.required}">*</span></label>
    <div class='input'>
      <input type='file' name='file[]' id="input-file" multiple required />
      <span class='help-inline'>{$lang.media.info.upload}</span>
    </div>
  </div>
  <div class='clearfix'>
    <label for='input-rename'>{$lang.media.label.rename}</label>
    <div class='input'>
      <input type='text' name='rename' id="input-rename" onkeyup="this.value = candy.system.stripNoAlphaChars(this.value)" />
    </div>
  </div>
  <div id="js-loading" class="center"></div>
  <div class="actions">
    <input type='hidden' value='formdata' name='create_file' />
    <input type='submit' class='btn primary' value='{$lang.media.title.create}' />
  </p>
</form>
<script type='text/javascript' src='%PATH_JS%/core/jquery.tiptip{$_compress_files_suffix_}.js'></script>
<script type="text/javascript">
  $("input[type='submit']").click(function() {
    $(this).val(lang.loading);
    $('#js-loading').html("<img src='%PATH_IMAGES%/loading.gif' alt='" + lang.loading + "' widht='32' height='32 />");
  });

  $('p.error').tipTip({ maxWidth: "auto" });
</script>