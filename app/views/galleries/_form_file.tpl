<form action='{$_action_url_}' method='post' enctype='multipart/form-data' id='js-upload'>
  <h1>{$lang_headline}</h1>
  {if $smarty.get.action == 'createfile'}
    <p>
      <label for='file'>{$lang_file_choose}</label>
      <input type='file' name='file[]' multiple />
    </p>
    <p>
      <label for='cut'>{$lang_cut}</label>
      <select name='cut'>
        <option value='c' {if $default == 'c'}default='default'{/if}>{$lang_create_file_cut}</option>
        <option value='r' {if $default == 'r'}default='default'{/if}>{$lang_create_file_resize}</option>
      </select>
    </p>
  {/if}
  <p>
    <label for='description'>{$lang_description}</label>
    <input type='text' name='description' value='{$description}' />
  </p>
  <p class="center">
    <input type='hidden' value='formdata' name='{$_formdata_}' />
    <input type='submit' value='{$lang_headline}' disabled />
    {if $smarty.get.action == 'updatefile'}
      <input type='reset' value='{$lang_reset}' />
      <input type='button' value='{$lang_destroy}' onclick="confirmDelete('/gallery/{$_request_id_}/destroyfile')" />
    {/if}
  </p>
</form>
<script type="text/javascript">
  $("input[type='file']").change(function(){
    if ($(this).val()) {
      $("input[type='submit']").attr('disabled',false);
    }
  });

  $("input[type='submit']").click(function() {
    $(this).val(LANG_LOADING).attr('disabled',true);
  });
</script>