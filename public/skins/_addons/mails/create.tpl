<form method='post' action='/mail/1'>
  <table>
    <tr>
      <th colspan='2'>{$lang_headline} {$contact.name} {$contact.surname}</th>
    </tr>
    <tr class='row1{if $error_content} error{/if}'>
      <td class='td_left'>
        <label for='content'>Ihre Nachricht</label>
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
    <tr class='row2{if $error_name} error{/if}'>
      <td class='td_left'>
        <label for='name'>Wer sind Sie?</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='name' id='name' value='{$name}' type='text' />
          {if $error_name}
            <div class="description">{$error_name}</div>
          {/if}
        </div>
      </td>
    </tr>
    <tr class='row1'>
      <td class='td_left'>
        Wie können wie Sie erreichen?
      </td>
      <td class='td_right'>
        <div class="checkbox">
          <input name='contact_via_mail' class='' id='contact_via_mail'
                 value='{$subject}' type='checkbox' onclick="showEmail()" />
          <label for='contact_via_mail'>E-Mail</label>
          <input name='contact_via_phone' class='' id='contact_via_phone'
                 value='{$subject}' type='checkbox' onclick="showPhone()" />
          <label for='contact_via_phone'>Telefon</label>
        </div>
      </td>
    </tr>
    <tr class='row2 email_hidden'>
      <td class='td_left'>
        <label for='subject'>Ihre E-Mail</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='subject' class='' id='subject'
                 value='{$subject}' type='text' />
        </div>
      </td>
    </tr>
    <tr class='row1 email_hidden'>
      <td class='td_left'>
        <label for='subject'>Betreff</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='subject' class='' id='subject'
                 value='{$subject}' type='text' />
        </div>
      </td>
    </tr>
    <tr class='row2 phone_hidden'>
      <td class='td_left'>
        <label for='subject'>Ihre Telefonnummer</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='subject' class='' id='subject'
                 value='{$subject}' type='text' />
        </div>
      </td>
    </tr>
  </table>
  <div class="submit">
    <input type='submit' value='Absenden' />
  </div>
  <input type='hidden' value='formdata' name='send_mail' />
</form>
<style>
  .email_hidden, .phone_hidden {
    display:none;
}
</style>
<script>
  function showEmail() {
    $each($$('.email_hidden'), function(el) {
      el.set('class', '');
    });
  };

  function showPhone() {
    $each($$('.phone_hidden'), function(el) {
      el.set('class', '');
    });
  }
</script>