<form action='/media/create' method='post' enctype='multipart/form-data'>
  <h1>{$lang.media.title.create}</h1>
  <p>
    <label for='input-file'>{$lang.media.label.choose} <span title="{$lang.global.required}">*</span></label>
    <input type='file' name='file[]' id="input-file" multiple required />
    <span class='description'>{$lang.media.info.upload}</span>
  </p>
  <p>
    <label for='input-rename'>{$lang.media.label.rename}</label>
    <input type='text' name='rename' id="input-rename" onkeyup="this.value = candy.system.stripNoAlphaChars(this.value)" />
  </p>
  <div id="js-loading" class="center"></div>
  <p class="center">
    <input type='hidden' value='formdata' name='create_file' />
    <input type='submit' value='{$lang.media.title.create}' />
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