<form method='post' action='/Mail/{$_request_id_}'>
  <table>
    <tr>
      <th colspan='2'>{$lang_headline} {$contact.name} {$contact.surname}</th>
    </tr>
    <tr class='row1{if $error_email} error{/if}'>
      <td class='td_left'>
        <label for='email'>{$lang_email}</label>
      </td>
      <td class='td_right'>
        {if $email == ''}
          <div class="input">
            <input name='email' id='email' value='{$email}' type='text' />
            {if $error_email}
              <div class="description">{$error_email}</div>
            {/if}
          </div>
        {else}
          {$email}
          <input name='email' id='email' value='{$email}' type='hidden' />
        {/if}
      </td>
    </tr>
    <tr class='row2'>
      <td class='td_left'>
        <label for='subject'>{$lang_subject}</label>
        ({$lang_optional})
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='subject' class='inputtext' id='subject'
                 value='{$subject}' type='text' />
        </div>
      </td>
    </tr>
    <tr class='row1{if $error_content} error{/if}'>
      <td class='td_left'>
        <label for='content'>{$lang_content}</label>
      </td>
      <td class='td_right'>
        <div class="textarea">
          <textarea name='content' id='content'
                    rows='12' cols='50'>{$content}</textarea>
          {if $error_content}
            <div class="description">{$error_content}</div>
          {/if}
        </div>
      </td>
    </tr>
  </table>
  <br />
  <center>
    <script type="text/javascript">
      var RecaptchaOptions = {
         lang : 'de'
      };
    </script>
    <div class="{if $error_captcha}error{/if}">
      {$_captcha_}
      {if $error_captcha}
        <div class="description">{$error_captcha}</div>
      {/if}
    </div>
  </center>
  <div class="submit">
    <input type='submit' value='{$lang_submit}' />
  </div>
  <input type='hidden' value='formdata' name='send_mail' />
</form>