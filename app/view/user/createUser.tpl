<form method='post' action='/Register'>
  <table>
    <tr>
      <th colspan='2'>{$lang_headline}</th>
    </tr>
    <tr class='row1'>
      <td class='left'>
        <label for='name'>{$lang_name}</label>
      </td>
      <td class='right'>
        <input name='name' id='name' class='inputtext'
               value='{$name}' type='text' />
      </td>
    </tr>
    <tr class='row2'>
      <td class='left'>
        <label for='surname'>{$lang_surname} ({$lang_optional})</label>
      </td>
      <td class='right'>
        <input name='surname' id='surname' class='inputtext'
               value='{$surname}' type='text' />
      </td>
    </tr>
    <tr class='row1'>
      <td class='left'>
        <label for='email'>{$lang_email}</label>
      </td>
      <td class='right'>
        <input name='email' class='inputtext' id='email'
               value='{$email}' type='text' />
      </td>
    </tr>
    <tr class='row2'>
      <td class='left'>
        <label for='password'>{$lang_password}</label>
      </td>
      <td class='right'>
        <input name='password' class='inputtext' id='password'
               value='' type='password' />
      </td>
    </tr>
    <tr class='row1'>
      <td class='left'>
        <label for='password2'>{$lang_password_repeat}</label>
      </td>
      <td class='right'>
        <input name='password2' class='inputtext' id='password2'
               value='' type='password' />
      </td>
    </tr>
    <tr class='row2'>
      <td class='left'>
        <a href='/Help/Registration' target='_blank'>{$lang_disclaimer_read}</a>
      </td>
      <td class='right'>
        <input name='disclaimer' value='' type='checkbox' />
      </td>
    </tr>
  </table>
  <input type='submit' class='inputbutton' value='{$lang_submit}' />
  <input type='hidden' value='formdata' name='create_user' />
</form>