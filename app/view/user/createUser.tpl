<form method='post' action='/Register'>
  <table>
    <tr>
      <th colspan='2'>{$lang_headline}</th>
    </tr>
    <tr class='row1'>
      <td class='td_left'>
        <label for='name'>{$lang_name}</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='name' id='name' value='{$name}' type='text' />
        </div>
      </td>
    </tr>
    <tr class='row2'>
      <td class='td_left'>
        <label for='surname'>{$lang_surname} ({$lang_optional})</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='surname' id='surname' value='{$surname}' type='text' />
        </div>
      </td>
    </tr>
    <tr class='row1'>
      <td class='td_left'>
        <label for='email'>{$lang_email}</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='email' id='email' value='{$email}' type='text' />
        </div>
      </td>
    </tr>
    <tr class='row2'>
      <td class='td_left'>
        <label for='password'>{$lang_password}</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='password' class='inputtext' id='password'
                 value='' type='password' />
        </div>
      </td>
    </tr>
    <tr class='row1'>
      <td class='td_left'>
        <label for='password2'>{$lang_password_repeat}</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='password2' class='inputtext' id='password2'
                 value='' type='password' onkeyup="checkPasswords('%PATH_IMAGES%/icons/')" />
          <img id="icon" src='%PATH_IMAGES%/spacer.gif' class="icon-close" alt="" />
        </div>
      </td>
    </tr>
    {if $UR < 4}
      <tr class='row2'>
        <td class='td_left'>
          <a href='/Help/Registration' target='_blank'>{$lang_disclaimer_read}</a>
        </td>
        <td class='td_right'>
          <div class="checkbox">
            <input name='disclaimer' value='' type='checkbox' />
          </div>
        </td>
      </tr>
    {/if}
  </table>
  <div class="submit">
    <input type='submit' class='inputbutton' value='{$lang_submit}' />
  </div>
  <input type='hidden' value='formdata' name='create_user' />
</form>