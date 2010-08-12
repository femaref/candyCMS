<form method='post' action='/Invite'>
  <table>
    <tr>
      <th colspan='2'>{$lang_headline}</th>
    </tr>
    <tr class='row1'>
      <td class='td_left'>
        <label for='email'>{$lang_email_of_friend}</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='email' id='email' type='text' />
        </div>
      </td>
    </tr>
    <tr class='row2'>
      <td class='td_left'>
        <label for='content'>{$lang_content}</label>
      </td>
      <td class='td_right'>
        <div class="textarea">
          <textarea name='message' id='content'
                    rows='11' cols='50' disabled>{$message}</textarea>
        </div>
      </td>
    </tr>
    <tr class='row1'>
      <td class='td_left'>
        <label for='notes'>{$lang_own_message}</label>
      </td>
      <td class='td_right'>
        <div class="textarea">
          <textarea name='notes' id='notes' rows='8' cols='50'></textarea>
        </div>
      </td>
    </tr>
  </table>
  <div class="submit">
    <input type='submit' value='{$lang_submit}' />
  </div>
  <input type='hidden' value='formdata' name='invite_friend' />
</form>