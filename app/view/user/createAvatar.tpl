<form id='js-upload_image' style='{$style}' action='{$action}' method='post' enctype='multipart/form-data'>
  <table>
    <tr>
      <th colspan='2'>{$lang_image_upload}</th>
    </tr>
    <tr class='row1'>
      <td class='td_left'>
        <label for='image'>{$lang_image_choose}</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input type='file' name='image' id='image' />
        </div>
        <div class='description'>{$lang_image_upload_info}</div>
      </td>
    </tr>
    <tr class='row2'>
      <td class='td_left'>
        <label for='agreement'>{$lang_image_agreement}</label>
      </td>
      <td class='td_right'>
        <div class="checkbox">
          <input type='checkbox' id='agreement' name='agreement' value='1' />
        </div>
      </td>
    </tr>
  </table>
  <div class="submit">
    <input type='submit' value='{$lang_image_upload}' />
  </div>
  <input type='hidden' value='formdata' name='create_avatar' />
  <input type='hidden' name='MAX_FILE_SIZE' value='409600' />
</form>