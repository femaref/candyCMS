<a name='create'></a>
<div id="create_comment">
  <form action='{$_action_url_}' method='post'>
    <fieldset>
      <legend>{$lang_headline}</legend>
      <table>
        <tr class='row1{if $error_name} error{/if}'>
          <td class='td_left'>
            <label for="name">{$lang_name}</label>
          </td>
          <td class='td_right'>
            {if $USER_NAME}
              {$USER_NAME} {$USER_SURNAME}
            {else}
              <div class="input">
                <input type="text" value="{$name}" name="name" id="name" />
                {if $error_name}
                  <div class="description">{$error_name}</div>
                {/if}
              </div>
            {/if}
          </td>
        </tr>
        <tr class='row2'>
          <td class='td_left'>
            <label for="name">{$lang_email} ({$lang_optional})</label>
          </td>
          <td class='td_right'>
            {if $USER_EMAIL}
              {$USER_EMAIL}
            {else}
              <div class="input">
                <input type="text" value="{$email}" name="email" id="email" />
                <div class='description'>{$lang_email_info}</div>
              </div>
            {/if}
          </td>
        </tr>
        <tr class='row1{if $error_content} error{/if}'>
          <td class='td_left'>
            <label for='js-create_commment_text'>{$lang_content}</label>
          </td>
          <td class='td_right'>
            <div class="textarea">
              <textarea name='content' id='js-create_commment_text' rows='10' cols='50'>{$content}</textarea>
              {if $error_content}
                <div class="description">{$error_content}</div>
              {else}
                <div class='description'>
                  <img src="%PATH_IMAGES%/spacer.png" class="icon-redirect" alt="" />
                  <a href='/Help/BB-Code' target='_blank'>{$lang_bb_help}</a>
                </div>
              {/if}
            </div>
          </td>
        </tr>
      </table>
    </fieldset>
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
    <div class="button">
      <input type='button' value='{$lang_reset}'
             onclick="resetContent('createCommentText')" />
    </div>
    <input type='hidden' value='formdata' name='create_comment' />
    <input type='hidden' value='{$parent_id}' name='parent_id' />
    {if $error_parent_id}
      <div class="description">{$error_parent_id}</div>
    {/if}
  </form>
</div>