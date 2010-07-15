<a name='create'></a>
<form action='{$action}' method='post'>
  <fieldset>
    <legend>{$lang_headline}</legend>
    <div class="textarea">
      <textarea name='content' id='createCommentText' rows='8' cols='50'></textarea>
    </div>
    <div class='description'>
      <a href='/Help/BB-Code' target='_blank'>{$lang_bb_help}</a>
    </div>
  </fieldset>
  <div class="submit">
    <input type='submit' value='{$lang_submit}' />
  </div>
  <div class="button">
    <input type='button' value='{$lang_reset}'
           onclick="destroyContent('createCommentText')" />
  </div>
  <input type='hidden' value='formdata' name='create_comment' />
</form>