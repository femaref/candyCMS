<form action='/Media/create' method='post' enctype='multipart/form-data'>
  <table>
    <tr>
      <th colspan='2'>{$lang_headline}</th>
    </tr>
    <tr class='row1'>
      <td class='td_left'>
        <label for='file'>{$lang_file_choose}</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input type='file' name='file' id='file' />
        </div>
      </td>
    </tr>
    <tr class='row2'>
      <td class='td_left'>
        <label for='rename'>{$lang_file_rename}</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input type='text' name='rename' id='rename'
                 onkeyup="this.value = stripNoAlphaChars(this.value)" />
        </div>
      </td>
    </tr>
    <tr>
      <td colspan='2'>
        <div class='description'>{$lang_file_create_info}</div>
      </td>
    </tr>
  </table>
  <div class="submit">
    <input type='submit' value='{$lang_headline}' id="submit" />
  </div>
  <input type='hidden' value='formdata' name='upload_file' />
</form>
<script type="text/javascript">
  window.addEvent('domready', function() {
    $('submit').addEvent('click', function() {
      this.disabled = true;
      $('.description').set('html', "<img class='js-loading' src='%PATH_IMAGES%/loading.gif' alt='' />");
    });
  });
</script>