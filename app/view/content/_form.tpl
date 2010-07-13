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
  <fieldset class="l">
    <legend>{$lang_title}</legend>
    <input type='text' class='inputtext' name='title'
           title='{$lang_title}' value='{$c.title}' />
    <div class='element_header_date'>
      {if $smarty.get.action == 'edit'}
        {$lang_last_update}: {$c.date}
      {/if}
    </div>
  </fieldset>
  <fieldset class="l">
    <legend>{$lang_content}</legend>
    <textarea name='content' title='{$lang_content}'
              rows='20' cols='75'>{$c.content}</textarea>
  </fieldset>
  <input type='submit' class='inputbutton' value='{$lang_submit}' />
	{if $smarty.get.action == 'edit'}
      <input type='reset' class='inputbutton' value='{$lang_reset}' />
      <input type='button' class='inputbutton' value='{$lang_destroy}' style='color:red'
             onclick="confirmDelete('{$c.title}', '/Content/destroy/{$id}')" />
	{/if}
  <input type='hidden' value='{$id}' name='id' />
  <input type='hidden' value='formdata' name='{$formdata}' />
</form>