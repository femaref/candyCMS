<a name='create'></a>
<form action='{$action}' method='post'>
  <fieldset>
    <legend>{$lang_headline}</legend>
    <textarea name='content' id='createCommentText' rows='8' cols='50'></textarea>
    <div class='description'>
      <a href='/Help/BB-Code' target='_blank'>{$lang_bb_help}</a>
    </div>
  </fieldset>
  <input class='inputbutton' type='submit' value='{$lang_submit}' />
  <input type='hidden' value='formdata' name='create_comment' />
</form>