<div class='page-header'>
  <h1>{$lang.media.title.create}</h1>
</div>
<form action='/media/create' method='post' enctype='multipart/form-data' class='form-horizontal'>
  <div class='control-group'>
    <label for='input-file' class='control-label'>
      {$lang.media.label.choose} <span title='{$lang.global.required}'>*</span>
    </label>
    <div class='controls'>
      <input type='file' name='file[]' id='input-file'
             class='span4 required' multiple required />
      <span class='help-block'>
        {$lang.media.info.upload}
      </span>
    </div>
  </div>
  <div class='control-group'>
    <label for='input-rename' class='control-label'>
      {$lang.media.label.rename}
    </label>
    <div class='controls'>
      <input type='text' name='rename' id='input-rename' class='span4'
             onkeyup='this.value = candy.system.stripNoAlphaChars(this.value)' />
    </div>
  </div>
  <div id='js-loading' class='center'></div>
  <div class='form-actions'>
    <input type='submit' class='btn btn-primary' value='{$lang.media.title.create}' />
    <input type='hidden' value='formdata' name='create_file' />
  </div>
</form>
<script type='text/javascript'>
  $("input[type='submit']").click(function() {
    $(this).val(lang.loading);
    $('#js-loading').html("<img src='%PATH_IMAGES%/loading.gif' alt='' + lang.loading + '' widht='32' height='32 />");
  });
</script>