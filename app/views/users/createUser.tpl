<form method='post' action='/user/create'>
  <table>
    <tr>
      <th colspan='2'>
        <h1>{$lang_registration}</h1>
      </th>
    </tr>
    <tr class='row1{if $error_name} error{/if}'>
      <td class='td_left'>
        <label for='name'>{$lang_name}</label>
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
    <tr class='row1{if $error_email} error{/if}'>
      <td class='td_left'>
        <label for='email'>{$lang_email}</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='email' id='email' value='{$email}' type='text' />
          {if $error_email}
            <div class="description">{$error_email}</div>
          {/if}
        </div>
      </td>
    </tr>
    <tr class='row2{if $error_password} error{/if}'>
      <td class='td_left'>
        <label for='password'>{$lang_password}</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='password' class='inputtext' id='password'
                 value='' type='password' />
          {if $error_password}
            <div class="description">{$error_password}</div>
          {/if}
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
                 value='' type='password' onkeyup="checkPasswords()" />
          <img id="icon" src='%PATH_IMAGES%/spacer.png' class="icon-close" alt="" />
        </div>
      </td>
    </tr>
    {if $USER_RIGHT < 4}
      <tr class='row2{if $error_disclaimer} error{/if}'>
        <td class='td_left'>
          <a href='#reload' onclick="reloadPage('/help/Registration', '{$_public_folder_}')">
            {$lang_disclaimer_read}
          </a>
        </td>
        <td class='td_right'>
          <div class="checkbox">
            <input name='disclaimer' value='' type='checkbox' />
            {if $error_disclaimer}
              <div class="description">{$error_disclaimer}</div>
            {/if}
          </div>
        </td>
      </tr>
    {/if}
  </table>
  <div id="js-ajax_reload" name="reload" style="display:none"></div>
  <div class="submit">
    <input type='submit' class='inputbutton' value='{$lang_register}' />
  </div>
  <input type='hidden' value='formdata' name='create_user' />
</form>
