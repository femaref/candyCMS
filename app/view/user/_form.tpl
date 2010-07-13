<form method='post' action='/User/update/{$uid}'>
  <table>
    <tr>
      <th colspan='3'>{$lang_headline}</th>
    </tr>
    <tr class='row1'>
      <td class='left'>
        <label for='name'>{$lang_name} ({$lang_required})</label>
      </td>
      <td class='right'>
        <input name='name' value='{$name}' type='text'
               id='name' class='inputtext' />
      </td>
      <td rowspan='5' style='vertical-align:top'>
		{if $USERID === $uid}
          <a href="javascript:showDiv('uploadImage')" class='small'>
            <img src='%PATH_UPLOAD%/{$avatar100}' class='image' alt='{$name}' />
            <br />
            {$lang_image_change}
          </a>
        {/if}
      </td>
    </tr>
    <tr class='row2'>
      <td class='left'>
        <label for='surname'>{$lang_surname}</label>
      </td>
      <td class='right'>
        <input name='surname' value='{$surname}' type='text'
               id='surname' class='inputtext' />
      </td>
    </tr>
    <tr class='row1'>
      <td class='left'>
        <label for='email'>{$lang_email}</label>
      </td>
      <td class='right'>
        <input name='email' value='{$email}' type='text'
               id='email' class='inputtext' />
      </td>
    </tr>
    <tr class='row2'>
      <td class='left'>
        <label for='description'>{$lang_about_you}</label>
      </td>
      <td class='right'>
        <textarea name='description' id='description'
                  rows='6' cols='30'>{$description}</textarea>
      </td>
    </tr>
    <tr class='row1'>
      <td class='left'>
        <label for='newsletter_default'>{$lang_newsletter}</label>
      </td>
      <td class='right'>
        <input name='newsletter_default' value='1' type='checkbox'
               id='newsletter_default'
               {if $newsletter_default == 1}checked='checked'{/if} />
      </td>
    </tr>
    {if $UR == 4 && $USERID !== $uid}
      <tr class='row2'>
        <td class='left'>
          <label for='userright'>{$lang_userright}</label>
        </td>
        <td class='right'>
          <select name='userright' class='inputdropdown'>
            <option value='1' {if $userright == 1}selected='selectsed'{/if}>{$lang_userright_1}</option>
            <option value='2' {if $userright == 2}selected='selectsed'{/if}>{$lang_userright_2}</option>
            <option value='3' {if $userright == 3}selected='selectsed'{/if}>{$lang_userright_3}</option>
            <option value='4' {if $userright == 4}selected='selectsed'{/if}>{$lang_userright_4}</option>
          </select>
        </td>
      </tr>
    {/if}
  </table>
  {if $USERID == $uid}
    <p></p>
    <table>
      <tr>
        <th colspan='2'>{$lang_password_change}</th>
      </tr>
      <tr class='row1'>
        <td class='left'>
          <label for='oldpw'>{$lang_password_old}</label>
        </td>
        <td class='right'>
          <input name='oldpw' value='' type='password'
                 id='oldpw' class='inputtext' />
        </td>
      </tr>
      <tr class='row2'>
        <td class='left'>
          <label for='newpw'>{$lang_password_new}</label>
        </td>
        <td class='right'>
          <input name='newpw' value='' type='password'
                 id='newpw' class='inputtext' />
        </td>
      </tr>
      <tr class='row1'>
        <td class='left'>
          <label for='newpw2'>{$lang_password_repeat}</label>
        </td>
        <td class='right'>
          <input name='newpw2' value='' type='password'
                 id='newpw2' class='inputtext' />
        </td>
      </tr>
    </table>
  {/if}
  <input type='submit' class='inputbutton' value='{$lang_submit}' />
  <input type='hidden' value='formdata' name='update_user' />
</form>
<p></p>