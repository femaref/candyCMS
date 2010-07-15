<form method='post' action='{$action}'>
  <table>
    <tr>
      <th colspan='2'>{$lang_headline}</th>
    </tr>
    <tr class='row1'>
      <td class='td_left'>
        <label for='title'>{$lang_title}</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='title' value='{$title}' type='text' id='title' />
        </div>
      </td>
    </tr>
    <tr class='row2'>
      <td class='td_left'>
        <label for='tags'>{$lang_description}</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='description' value='{$description}' type='text'
              id='tags' />
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
        onclick="confirmDelete('{$title}', '/Gallery/destroy/{$id}')" />
    </div>
  {/if}
  <input type='hidden' value='{$id}' name='id' />
  <input type='hidden' value='formdata' name='{$formdata}' />
</form>