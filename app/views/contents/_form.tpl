<script language='javascript' type='text/javascript' src='%PATH_PUBLIC%/lib/tiny_mce/tiny_mce.js'></script>
<script language='javascript' type='text/javascript'>
  tinyMCE.init({
    mode : "textareas",
    theme : "advanced",
    entity_encoding : "raw",
    content_css : "%PATH_CSS%/tinymce{$_compress_files_suffix_}.css",
    plugins : "autosave,safari,pagebreak,style,table,advimage,advlink,emotions,inlinepopups,insertdatetime,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
    theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,styleselect,formatselect,fontselect,fontsizeselect",
    theme_advanced_buttons2 : "cut,copy,paste,pastetext,|,search,replace,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,|,forecolor,backcolor,|,pagebreak,|,fullscreen",
    theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,media,|,print,|,ltr,rtl,|,help",
    theme_advanced_statusbar_location : "bottom",
    theme_advanced_resizing : true,
    height : "300px",
    language : "{$_language_}",
    relative_urls : false,
    remove_script_host : false,
    document_base_url : "{$WEBSITE_URL}"
  });
</script>
<form method='post' action='{$_action_url_}'>
  <h1>_Content bearbeiten_</h1>
    {if $smarty.get.action == 'update'}
      <p>{$lang_last_update}: {$c.date}</p>
    {/if}
  <p {if $error_title}class="error"{/if}>
    <label for="title">{$lang_title} *</label>
    <input type='text' name='title' title='{$lang_title}' value='{$c.title}' autofocus required />
  </p>
  <p {if $error_content}class="error"{/if}>
    <label for="title">{$lang_content} *</label>
    <textarea name='content' title='{$lang_content}' required>{$c.content}</textarea>
  </p>
  <p class="center">
    <input type='submit' value='{$lang_submit}' />
    <input type='hidden' value='{$_request_id_}' name='id' />
    <input type='hidden' value='formdata' name='{$_formdata_}' />
    {if $smarty.get.action == 'update'}
      <input type='reset' value='{$lang_reset}' />
      <input type='button' value='{$lang_destroy_entry}'
             onclick="confirmDelete('/content/{$_request_id_}/destroy')" />
    {/if}
  </p>
</form>