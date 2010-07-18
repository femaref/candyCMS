<form method='post' action='/Mail/{$id}'>
  <table>
    <tr>
      <th colspan='2'>{$lang_headline}</th>
    </tr>
    <tr class='row1'>
      <td class='td_left'>
        <label for='email'>{$lang_email}</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='email' id='email' value='{$email}' type='text'
                {if $email !== ''}disabled='disabled'{/if}/>
        </div>
      </td>
    </tr>
    <tr class='row2'>
      <td class='td_left'>
        <label for='subject'>{$lang_subject}</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='subject' class='inputtext' id='subject'
                 value='{$subject}' type='text' />
        </div>
      </td>
    </tr>
    <tr class='row1'>
      <td class='td_left'>
        <label for='content'>{$lang_content}</label>
      </td>
      <td class='td_right'>
        <div class="textarea">
          <textarea name='content' id='content'
                    rows='12' cols='50'>{$content}</textarea>
        </div>
      </td>
    </tr>
  </table>
  <br />
  <center>
    {$captcha}
  </center>
  <div class="submit">
    <input type='submit' value='{$lang_submit}' />
  </div>
  <input type='hidden' value='formdata' name='send_mail' />
</form>