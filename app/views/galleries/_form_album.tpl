<form method='post' action='{$_action_url_}'>
  <table>
    <tr>
      <th colspan='2'>{$lang_headline}</th>
    </tr>
    <tr class='row1{if $error_title} error{/if}'>
      <td class='td_left'>
        <label for='title'>{$lang_title}</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='title' value='{$title}' type='text' id='title' />
          {if $error_title}
            <div class="description">{$error_title}</div>
          {/if}
        </div>
      </td>
    </tr>
    <tr class='row2'>
      <td class='td_left'>
        <label for='tags'>{$lang_description}</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='description' value='{$description}' type='text' id='tags' />
        </div>
      </td>
    </tr>
  </table>
  <div class="submit">
    <input type='submit' class='inputbutton' value='{$lang_submit}' />
  </div>
  {if $smarty.get.action == 'update'}
    <div class="button">
      <input type='reset' value='{$lang_reset}' />
    </div>
    <div class="cancel">
      <input type='button' value='{$lang_destroy_entry}'
        onclick="confirmDelete('/gallery/{$_request_id_}/destroy')" />
    </div>
  {/if}
  <input type='hidden' value='{$_request_id_}' name='id' />
  <input type='hidden' value='formdata' name='{$_formdata_}' />
</form>