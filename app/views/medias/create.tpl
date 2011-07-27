<form action='/media/create' method='post' enctype='multipart/form-data'>
  <h1>{$lang_headline}</h1>
  <p>
    <label for='file'>{$lang_file_choose} *</label>
    <input type='file' name='file' />
    <span class='description'>{$lang_file_create_info}</span>
  </p>
  <p>
    <label for='rename'>{$lang_file_rename}</label>
    <input type='text' name='rename' onkeyup="this.value = candy.system.stripNoAlphaChars(this.value)" />
  </p>
  <p class="center">
    <input type='hidden' value='formdata' name='upload_file' />
    <input type='submit' value='{$lang_headline}' disabled />
  </p>
</form>
<script type="text/javascript">
  $("input[type='file']").change(function(){
    if ($(this).val()) {
      $("input[type='submit']").attr('disabled',false);
    }
  });

  if(!$.browser.opera) {
    $("input[type='submit']").click(function() {
      $(this).val(LANG_LOADING).attr('disabled',true);
    });
  }
</script>