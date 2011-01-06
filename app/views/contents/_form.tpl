<script language='javascript' type='text/javascript'
src='%PATH_PUBLIC%/lib/tiny_mce/tiny_mce.js'></script>
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
  <fieldset class="left{if $error_title} error{/if}">
    <legend>
      {$lang_title}
    </legend>
    <div class="input">
      <input type='text' name='title' title='{$lang_title}' value='{$c.title}' />
      {if $error_title}
        <div class="description">{$error_title}</div>
      {/if}
    </div>
    {if $smarty.get.action == 'update'}
      &nbsp;
      {$lang_last_update}: {$c.date}
    {/if}
  </fieldset>
  <fieldset class="left{if $error_content} error{/if}">
    <legend>{$lang_content}</legend>
    <div class="textarea">
      <textarea name='content' title='{$lang_content}'
                rows='20' cols='75'>{$c.content}</textarea>
      {if $error_content}
        <div class="description">{$error_content}</div>
      {else}
        <div class='description center'>
          <img src="%PATH_IMAGES%/spacer.png" class="icon-redirect" alt="" />
          <a href='/help/BB-Code' target='_blank'>{$lang_bb_help}</a>
        </div>
      {/if}
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
    <input type='button' value='{$lang_destroy_entry}'
           onclick="confirmDelete('/content/{$_request_id_}/destroy')" />
  </div>
  {/if}
  <input type='hidden' value='{$_request_id_}' name='id' />
  <input type='hidden' value='formdata' name='{$_formdata_}' />
</form>