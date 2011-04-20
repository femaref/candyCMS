<form action='/media/create' method='post' enctype='multipart/form-data'>
  <h1>{$lang_headline}</h1>
  <p>
    <label for='file'>{$lang_file_choose} *</label>
    <input type='file' name='file' />
  </p>
  <p>
    <label for='rename'>{$lang_file_rename}</label>
    <input type='text' name='rename' onkeyup="this.value = stripNoAlphaChars(this.value)" />
    <span class='description'>{$lang_file_create_info}</span>
  </p>
  <p class="center">
    <input type='hidden' value='formdata' name='upload_file' />
    <input type='submit' value='{$lang_headline}' id="submit" />
  </p>
</form>
<script type="text/javascript">
  // TODO: Reset loading button
  //window.addEvent('domready', function() {
  //  document.id('submit').addEvent('click', function() {
  //    this.disabled = true;
  //    document.id('.description').set('html', "<img class='js-loading' src='%PATH_IMAGES%/loading.gif' alt='' />");
  //  });
  //});
</script>