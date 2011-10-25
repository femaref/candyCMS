<form method='post' action='/content/{$smarty.get.action}'>
  <h1>{if $smarty.get.action == 'create'}{$lang.content.title.create}{else}{$lang.content.title.update|replace:'%p':$title}{/if}</h1>
  <p {if isset($error_title)}class="error" title="{$error_title}"{/if}>
    <label for="input-title">{$lang.global.title} <span title="{$lang.global.required}">*</span></label>
    <input type='text' name='title' title='{$lang.global.title}' value='{$title}' id="input-title" autofocus required />
  </p>
  <p>
    <label for='input-teaser'>
      {$lang.global.teaser}
    </label>
    <input name='teaser' value='{$teaser}' type='text' placeholder='{$lang.content.info.teaser}'
           title='{$lang.content.info.teaser}' id="input-teaser" />
    <span id="js-count_chars">160</span>
  </p>
  <p>
    <label for='input-keywords'>{$lang.global.keywords}</label>
    <input name='keywords' value='{$keywords}' type='text' placeholder='{$lang.content.info.keywords}' title='{$lang.content.info.keywords}' id="input-keywords" />
  </p>
  <p {if isset($error_content)}class="error" title="{$error_content}"{/if}>
    <label for="input-content">{$lang.global.content} *</label>
    <textarea name='content' title='{$lang.global.content}' class="js-tinymce" id="input-content">{$content}</textarea>
  </p>
  <p class="center">
    <input type='submit' value="{if $smarty.get.action == 'create'}{$lang.content.title.create}{else}{$lang.global.update.update}{/if}" />
    <input type='hidden' value='formdata' name='{$smarty.get.action}_content' />
    {if $smarty.get.action == 'update'}
      <input type='hidden' value='{$_request_id_}' name='id' />
      <input type='reset' value='{$lang.global.reset}' />
      <input type='button' value='{$lang.content.title.destroy}'
             onclick="candy.system.confirmDestroy('/content/{$_request_id_}/destroy')" />
    {/if}
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
      language : "{$WEBSITE_LANGUAGE}",
      relative_urls : false,
      remove_script_host : false,
      document_base_url : "{$WEBSITE_URL}",
      entity_encoding : "raw",
      height : "300px",
      content_css : "%PATH_CSS%/core/tinymce{$_compress_files_suffix_}.css"
    });

    $('#js-count_chars').bind('keyup', function() {
      var iLength = 160 - $(this).val().length;
      this.html(iLength);
    });
  });
</script>