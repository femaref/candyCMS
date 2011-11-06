<form method='post' action='/blog/{$smarty.get.action}' enctype="multipart/form-data">
  <h1>{if $smarty.get.action == 'create'}{$lang.blog.title.create}{else}{$lang.blog.title.update|replace:'%p':$title}{/if}</h1>
  <p {if isset($error.title)}class="error" title="{$error.title}"{/if}>
    <label for='input-title'>{$lang.global.title}<span title="{$lang.global.required}">*</span></label>
    <input name='title' value='{$title}' type='text' id="input-title" required />
  </p>
  <p>
    <label for='input-teaser'>
      {$lang.global.teaser}
    </label>
    <input name='teaser' value='{$teaser}' type='text' placeholder='{$lang.blog.info.teaser}'
           title='{$lang.blog.info.teaser}' id="input-teaser" />
    <span id="js-count_chars">160</span>
  </p>
  <p>
    <label for='input-tags'>{$lang.global.tags.tags}</label>
    <input name='tags' value='{$tags}' type='text' placeholder='{$lang.blog.info.tag}' title='{$lang.blog.info.tag}' id="input-tags" />
  </p>
  <p>
    <label for='input-keywords'>{$lang.global.keywords}</label>
    <input name='keywords' value='{$keywords}' type='text' placeholder='{$lang.blog.info.keywords}' title='{$lang.blog.info.keywords}' id="input-keywords" />
  </p>
  <p {if isset($error.content)}class="error" title="{$error.content}"{/if}>
    <label for='input-content'>{$lang.global.content}<span title="{$lang.global.required}">*</span></label>
    <textarea name='content' class="js-tinymce" rows='16' cols='50' id="input-content">{$content}</textarea>
  </p>
  <p>
    <label for='input-published'>{$lang.global.published}</label>
    <input name='published' value='1' type='checkbox' id="input-published" {if $published == true}checked{/if} />
  </p>
  {if $smarty.get.action === 'update'}
    <p>
      <label for='input-show_update'>{$lang.global.update.show}</label>
      <input type='checkbox' name='show_update' value='1' id="input-show_update" />
    </p>
  {/if}
  <p class="center">
    <input type='hidden' value='{$author_id}' name='author_id' />
    <input type='hidden' value='formdata' name='{$smarty.get.action}_blog' />
    <input type='submit' value="{if $smarty.get.action == 'create'}{$lang.global.create.create}{else}{$lang.global.update.update}{/if}" />
    {if $smarty.get.action == 'update'}
      <input type='button' value='{$lang.blog.title.destroy}' onclick="candy.system.confirmDestroy('/blog/{$_request_id_}/destroy')" />
      <input type='hidden' value='{$_request_id_}' name='id' />
      <input type='reset' value='{$lang.global.reset}' />
    {/if}
  </p>
</form>
<script type='text/javascript' src='/lib/tiny_mce/jquery.tinymce.js'></script>
<script type='text/javascript'>
  $(document).ready(function(){
    $('.js-tinymce').tinymce({
      script_url : '/lib/tiny_mce/tiny_mce.js',
      theme : "advanced",
      plugins : "autosave,safari,pagebreak,style,advimage,advlink,emotions,inlinepopups,insertdatetime,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
      theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,cut,copy,paste,pastetext,|,search,replace,|,fullscreen",
      theme_advanced_buttons2 : "styleselect,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor",
      theme_advanced_buttons3 : "hr,|,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,|,outdent,indent,|,pagebreak,|,charmap,emotions,media,|,print",
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