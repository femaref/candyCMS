<form method='post' action='{$_action_url_}' class="form blog">
  <h1>{$lang_headline}</h1>
  <p {if $error_title}class="error"{/if}>
    <label for='title'>{$lang_title} *</label>
    <input name='title' value='{$title}' type='text' id='title' required />
  </p>
  <p>
    <label for='tags'>{$lang_tags}</label>
    <input name='tags' value='{$tags}' type='text' id='tags' class="js-tooltip" title='{$lang_create_tag_info}' />
  </p>
  <p>
    <label for='teaser'>
      {$lang_teaser}
      <span id="js-chars">140</span>
    </label>
    <input name='teaser' value='{$teaser}' type='text' id='teaser' title='{$lang_create_teaser_info}'
           onkeyup="$('#js-chars').html(140 - $(this).val().length)" class="js-tooltip" />
  </p>
  <p {if $error_content}class="error"{/if}>
    <label for='form-content'>{$lang_content} *</label>
    <textarea name='content' class="js-tinymce"
              id='form-content' rows='16' cols='50' required>{$content}</textarea>
  </p>
  <p>
    <label for='published'>{$lang_published}</label>
    <input name='published' value='1' type='checkbox'
           id='published' {if $published == true}checked='checked'{/if} />
  </p>
  {if $smarty.get.action == 'update'}
    <p>
      <label for='show_update'>{$lang_update_show}</label>
      <input type='checkbox' id='show_update'
             name='show_update' value='1' />
    </p>
  {/if}
  <p class="center">
    <input type='hidden' value='{$_request_id_}' name='id' />
    <input type='hidden' value='{$author_id}' name='author_id' />
    <input type='hidden' value='formdata' name='{$_formdata_}' />
    <input type='submit' value='{$lang_submit}' />
      {if $smarty.get.action == 'update'}
        <input type='reset' value='{$lang_reset}' />
        <input type='button' value='{$lang_destroy_entry}'
               onclick="confirmDelete('/blog/{$_request_id_}/destroy')" />
      {/if}
  </p>
</form>
<script language='javascript' type='text/javascript' src='%PATH_PUBLIC%/js/core/jquery.tipTip{$_compress_files_suffix_}.js'></script>
<script language='javascript' type='text/javascript' src='%PATH_PUBLIC%/lib/tiny_mce/jquery.tinymce.js'></script>
<script language='javascript' type='text/javascript'>
  $(document).ready(function(){
    $('.js-tinymce').tinymce({
      script_url : '%PATH_PUBLIC%/lib/tiny_mce/tiny_mce.js',
      theme : "advanced",
      plugins : "autosave,safari,pagebreak,style,advimage,advlink,emotions,inlinepopups,insertdatetime,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
      theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,cut,copy,paste,pastetext,|,search,replace,|,fullscreen",
      theme_advanced_buttons2 : "styleselect,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor",
      theme_advanced_buttons3 : "hr,|,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,|,outdent,indent,|,pagebreak,|,charmap,emotions,media,|,print",
      theme_advanced_statusbar_location : "bottom",
      theme_advanced_resizing : true,
      language : "{$_language_}",
      relative_urls : false,
      remove_script_host : false,
      document_base_url : "{$WEBSITE_URL}",
      entity_encoding : "raw",
      height : "200px",
      content_css : "%PATH_CSS%/tinymce{$_compress_files_suffix_}.css"
    });
    $('.js-tooltip').tipTip({ maxWidth:"auto",edgeOffset: 15, activation:"focus" });
  });
</script>