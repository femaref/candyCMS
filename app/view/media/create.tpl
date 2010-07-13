<form action='/Media/create' method='post' enctype='multipart/form-data'>
  <table>
    <tr>
      <th colspan='2'>{$lang_headline}</th>
    </tr>
    <tr class='row1'>
      <td class='left'>
        <label for='file'>{$lang_file_choose}</label>
      </td>
      <td class='right'>
        <input type='file' name='file' id='file' />
      </td>
    </tr>
    <tr class='row2'>
      <td class='left'>
        <label for='rename'>{$lang_file_rename}</label>
      </td>
      <td class='right'>
        <input type='text' class='inputtext' name='rename' id='rename'
               onkeyup="this.value = stripNoAlphaChars(this.value)" />
      </td>
    </tr>
    <tr>
      <td colspan='2'>
        <div class='description'>{$lang_file_create_info}</div>
      </td>
    </tr>
  </table>
  <input type='submit' value='{$lang_headline}' class='inputbutton' />
  <input type='hidden' value='formdata' name='upload_file' />
</form>