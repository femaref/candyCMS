<form method='post' action='/blog/{$smarty.get.action}' enctype="multipart/form-data">
  <h1>{$lang_headline}</h1>
  <p {if isset($error_title)}class="error" title="{$error_title}"{/if}>
    <label for='input-title'>{$lang_title} <span title="{$lang_required}">*</span></label>
    <input name='title' value='{$title}' type='text' id="input-title" required />
  </p>
  <p>
    <label for='input-teaser'>
      {$lang_teaser}
    </label>
    <input name='teaser' value='{$teaser}' type='text' placeholder='{$lang_create_teaser_info}'
           title='{$lang_create_teaser_info}' id="input-teaser" onkeyup="$('#js-chars').html(160 - $(this).val().length)" />
    <span id="js-chars">160</span>
  </p>
  <p>
    <label for='input-tags'>{$lang_tags}</label>
    <input name='tags' value='{$tags}' type='text' placeholder='{$lang_create_tag_info}' title='{$lang_create_tag_info}' id="input-tags" />
  </p>
  <p>
    <label for='input-keywords'>{$lang_keywords}</label>
    <input name='keywords' value='{$keywords}' type='text' placeholder='{$lang_create_keywords_info}' title='{$lang_create_keywords_info}' id="input-keywords" />
  </p>
  <p {if isset($error_content)}class="error" title="{$error_content}"{/if}>
    <label for='input-content'>{$lang_content} *</label>
    <textarea name='content' class="js-tinymce" rows='16' cols='50' id="input-content">{$content}</textarea>
  </p>
  <p>
    <label for='input-published'>{$lang_published}</label>
    <input name='published' value='1' type='checkbox' id="input-published" {if $published == true}checked{/if} />
  </p>
  {if $smarty.get.action === 'update'}
    <p>
      <label for='input-show_update'>{$lang_update_show}</label>
      <input type='checkbox' name='show_update' value='1' id="input-show_update" />
    </p>
  {/if}
  <p class="center">
    <input type='hidden' value='{$author_id}' name='author_id' />
    <input type='hidden' value='formdata' name='{$smarty.get.action}_blog' />
    <input type='submit' value='{$lang_submit}' />
    {if $smarty.get.action == 'update'}
      <input type='button' value='{$lang_destroy_entry}' onclick="confirmDelete('/blog/{$_request_id_}/destroy')" />
      <input type='hidden' value='{$_request_id_}' name='id' />
      <input type='reset' value='{$lang_reset}' />
    {/if}
  </p>
</form>
<script type='text/javascript' src='%PATH_PUBLIC%/lib/tiny_mce/jquery.tinymce.js'></script>
<script type='text/javascript'>
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
      height : "300px",
      content_css : "%PATH_CSS%/tinymce{$_compress_files_suffix_}.css"
    });
  });
</script>