{literal}
  <script language='javascript' type='text/javascript'
  src='%PATH_PUBLIC%/lib/tiny_mce/tiny_mce.js'></script>
  <script language='javascript' type='text/javascript'>
    tinyMCE.init({
      mode : "textareas",
      theme : "advanced",
      theme_advanced_resize_horizontal : "true",
      entity_encoding : "raw",
      plugins : "safari,pagebreak,style,table,advimage,advlink,emotions,inlinepopups,insertdatetime,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
      theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,styleselect,formatselect,fontselect,fontsizeselect",
      theme_advanced_buttons2 : "cut,copy,paste,pastetext,|,search,replace,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,|,forecolor,backcolor,|,pagebreak,|,fullscreen",
      theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,media,|,print,|,ltr,rtl,|,help",
      theme_advanced_statusbar_location : "bottom",
      theme_advanced_resizing : true,
      height : "500px",
      width : "100%"
    });
  </script>
{/literal}
<form method='post' action='{$action}'>
  <fieldset class="left">
    <legend>
      {$lang_title}
    </legend>
    <div class="input">
      <input type='text' name='title' title='{$lang_title}' value='{$c.title}' />
    </div>
    {if $smarty.get.action == 'update'}
      {$lang_last_update}: {$c.date}
    {/if}
  </fieldset>
  <fieldset class="left">
    <legend>{$lang_content}</legend>
    <div class="textarea">
      <textarea name='content' title='{$lang_content}'
                rows='20' cols='75'>{$c.content}</textarea>
    </div>
  </fieldset>
  <div class="submit">
    <input type='submit' class='inputbutton' value='{$lang_submit}' />
  </div>
	{if $smarty.get.action == 'update'}
      <div class="button">
        <input type='reset' value='{$lang_reset}' />
      </div>
      <div class="cancel">
        <input type='button' value='{$lang_destroy}'
               onclick="confirmDelete('{$c.title}', '/Content/destroy/{$id}')" />
      </div>
	{/if}
  <input type='hidden' value='{$id}' name='id' />
  <input type='hidden' value='formdata' name='{$formdata}' />
</form>