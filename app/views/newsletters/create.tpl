<form method='post'>
  <h1>{$lang.newsletter.title.create}</h1>
  <p {if isset($error.subject)}class="error" title="{$error.subject}"{/if}>
    <label for='input-subject'>{$lang.global.subject}<span title="{$lang.global.required}">*</span></label>
    <input name='subject' value='{$subject}' type='text' id="input-subject" required autofocus />
  </p>
  <p {if isset($error.content)}class="error" title="{$error.content}"{/if}>
    <label for='input-content'>{$lang.global.content}<span title="{$lang.global.required}">*</span></label>
    <textarea name='content' rows='20' cols='50' class="js-tinymce" id="input-content" required>{$content}</textarea>
    <span class='description'>{$lang.newsletter.info.name}</span>
  </p>
  <p class="center">
    <input type='hidden' value='formdata' name='create_newsletter' />
    <input type='submit' value='{$lang.global.submit}' />
  </p>
</form>
<script type='text/javascript' src='/lib/tiny_mce/jquery.tinymce.js'></script>
<script type='text/javascript'>
  $(document).ready(function(){
    $('textarea.js-tinymce').tinymce({
      script_url : '/lib/tiny_mce/tiny_mce.js',
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
      content_css : "%PATH_CSS%/core/tinymce.css"
    });
  });
</script>