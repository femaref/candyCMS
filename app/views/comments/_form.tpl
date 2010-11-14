<a name='create'></a>
<div id="create_comment">
  <form action='{$_action_url_}' method='post'>
    <fieldset>
      <legend>{$lang_headline}{if !$USER_FACEBOOK_ID && !$USER_NAME} <fb:login-button></fb:login-button>{/if}</legend>
      <table>
        <tr class='row1{if $error_name} error{/if}'>
          <td class='td_left'>
            <label for="name">{$lang_name}</label>
          </td>
          <td class='td_right'>
            {if $USER_NAME}
              {$USER_FULL_NAME}
              {if $USER_FACEBOOK_ID}
                <input type="hidden" value="{$USER_FULL_NAME}" name="name" id="name" />
                <input type="hidden" value="{$USER_FACEBOOK_ID}" name="facebook_id" id="facebook_id" />
              {/if}
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
              {if $USER_FACEBOOK_ID}
                <input type="hidden" value="{$USER_EMAIL}" name="email" id="email" />
              {/if}
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
    {if $_captcha_}
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
    {/if}
    <div class="submit">
      <input type='submit' value='{$lang_submit}' />
    </div>
    <div class="button">
      <input type='button' value='{$lang_reset}'
             onclick="resetContent('createCommentText')" />
    </div>
    <input type='hidden' value='formdata' name='create_comment' />
    <input type='hidden' value='{$_parent_id_}' name='parent_id' />
    {if $error_parent_id}
      <div class="description">{$error_parent_id}</div>
    {/if}
  </form>
</div>