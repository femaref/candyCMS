<form method='post' action='{$_action_url_}'>
  <h1>{$lang_headline}</h1>
    {if $smarty.get.action == 'update'}
      <p>{$lang_last_update}: {$c.date}</p>
    {/if}
  <p {if isset($error_title)}class="error" title="{$error_title}"{/if}>
    <label for="input-title">{$lang_title} <span title="{$lang_required}">*</span></label>
    <input type='text' name='title' title='{$lang_title}' value='{$c.title}' id="input-title" autofocus required />
  </p>
  <p>
    <label for='input-teaser'>
      {$lang_teaser}
    </label>
    <input name='teaser' value='{$c.teaser}' type='text' placeholder='{$lang_create_teaser_info}'
           title='{$lang_create_teaser_info}' id="input-teaser" onkeyup="$('#js-chars').html(140 - $(this).val().length)" />
    <span id="js-chars">140</span>
  </p>
  <p>
    <label for='input-keywords'>{$lang_keywords}</label>
    <input name='keywords' value='{$c.keywords}' type='text' placeholder='{$lang_create_keywords_info}' title='{$lang_create_keywords_info}' id="input-keywords" />
  </p>
  <p {if isset($error_content)}class="error" title="{$error_content}"{/if}>
    <label for="input-content">{$lang_content} *</label>
    <textarea name='content' title='{$lang_content}' class="js-tinymce" id="input-content">{$c.content}</textarea>
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
<script type='text/javascript' src='%PATH_PUBLIC%/lib/tiny_mce/jquery.tinymce.js'></script>
<script type='text/javascript'>
  $(document).ready(function(){
    $('textarea.js-tinymce').tinymce({
      script_url : '%PATH_PUBLIC%/lib/tiny_mce/tiny_mce.js',
      theme : "advanced",
      plugins : "autosave,safari,pagebreak,style,advimage,advlink,emotions,inlinepopups,insertdatetime,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,table",
      theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,styleselect,formatselect,fontselect,fontsizeselect",
      theme_advanced_buttons2 : "cut,copy,paste,pastetext,|,search,replace,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,|,forecolor,backcolor,|,pagebreak,|,fullscreen",
      theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,media,|,print,|,ltr,rtl,|,help",
      theme_advanced_statusbar_location : "bottom",
      theme_advanced_resizing : true,
      language : "{$_language_}",
      relative_urls : false,
      remove_script_host : false,
      document_base_url : "{$WEBSITE_URL}",
      entity_encoding : "raw",
      height : "300px",
      content_css : "%PATH_CSS%/tinymce{$_compress_files_suffix_}.css"
    });
  });
</script>