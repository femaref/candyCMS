<h1>{if $smarty.get.action == 'create'}{$lang.blog.title.create}{else}{$lang.blog.title.update|replace:'%p':$title}{/if}</h1>
<form method='post' action='/blog/{$smarty.get.action}'>
  <div class="clearfix {if isset($error.title)} error{/if}">
    <label for='input-title'>{$lang.global.title} <span title="{$lang.global.required}">*</span></label>
    <div class="input">
      <input name='title' value='{$title}' type='text' id="input-title" required />
      {if isset($error.title)}<span class="help-inline">{$error.title}</span>{/if}
    </div>
  </div>
  <div class="clearfix">
    <label for='input-teaser'>
      {$lang.global.teaser}
    </label>
    <div class="input">
      <input name='teaser' value='{$teaser}' type='text' placeholder='{$lang.blog.info.teaser}'
             title='{$lang.blog.info.teaser}' id="input-teaser" />
      <span id="js-count_chars">160</span>
    </div>
  </div>
  <div class="clearfix">    
    <label for='input-tags'>{$lang.global.tags.tags}</label>
    <div class="input">
      <input name='tags' value='{$tags}' type='text' placeholder='{$lang.blog.info.tag}' title='{$lang.blog.info.tag}' id="input-tags" />
    </div>
  </div>
  <div class="clearfix">
    <label for='input-keywords'>{$lang.global.keywords}</label>
    <div class="input">
      <input name='keywords' value='{$keywords}' type='text' placeholder='{$lang.blog.info.keywords}' title='{$lang.blog.info.keywords}' id="input-keywords" />
    </div>
  </div>
  <div class="clearfix{if isset($error.content)} error{/if}">
    <label for='input-content'>{$lang.global.content} <span title="{$lang.global.required}">*</span></label>
    <div class="input">
      <textarea name='content' class="js-tinymce" rows='16' cols='50' id="input-content">{$content}</textarea>
      {if isset($error.content)}<span class="help-inline">{$error.content}</span>{/if}
    </div>
  </div>
  <div class="clearfix">
    <label for='input-published'>{$lang.global.published}</label>
    <div class="input">
      <ul class='inputs-list'>
        <li>
          <label>
            <input name='published' value='1' type='checkbox' id="input-published" {if $published == true}checked{/if} />
          </label>
        </li>
      </ul>
    </div>
  </div>
  {if $smarty.get.action == 'update'}
    <div class="clearfix">
      <label for='input-update_date'>{$lang.blog.label.date}</label>
      <div class="input">
        <ul class='inputs-list'>
          <li>
            <label>
              <input name='update_date' value='1' type='checkbox' id="input-update_date" />
            </label>
          </li>
        </ul>
      </div>
    </div>
    <div class="clearfix">
      <label for='input-show_update'>{$lang.global.update.show}</label>
      <div class="input">
        <ul class='inputs-list'>
          <li>
            <label>
              <input type='checkbox' name='show_update' value='1' id="input-show_update" {if $date_modified > 0}checked{/if} />
            </label>
          </li>
        </ul>
      </div>
    </div>
  {/if}
  <div class="actions">
    {if isset($author_id)}
      <input type='hidden' value='{$author_id}' name='author_id' />
    {/if}
    <input type='hidden' value='formdata' name='{$smarty.get.action}_blog' />
    <input type='submit' class='btn primary' value="{if $smarty.get.action == 'create'}{$lang.global.create.create}{else}{$lang.global.update.update}{/if}" />
    {if $smarty.get.action == 'update'}
      <input type='button' class='btn' value='{$lang.blog.title.destroy}' onclick="candy.system.confirmDestroy('/blog/{$_request_id_}/destroy')" />
      <input type='hidden' value='{$_request_id_}' name='id' />
      <input type='hidden' value='{$date}' name='date' />
      <input type='reset' class='btn' value='{$lang.global.reset}' />
    {/if}
  </div>
</form>
<script type='text/javascript' src='/lib/tiny_mce/jquery.tinymce.js'></script>
<script type='text/javascript' src='%PATH_JS%/core/jquery.tiptip{$_compress_files_suffix_}.js'></script>
<script type='text/javascript'>
  $(document).ready(function(){
    $('textarea.js-tinymce').tinymce({
      script_url : '/lib/tiny_mce/tiny_mce.js',
      theme : "advanced",
      plugins : "autosave,safari,style,advimage,advlink,inlinepopups,insertdatetime,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
      theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,cut,copy,paste,pastetext,|,search,replace,|,fullscreen",
      theme_advanced_buttons2 : "styleselect,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor",
      theme_advanced_buttons3 : "hr,|,link,unlink,anchor,|,image,|,cleanup,removeformat,|,code,|,insertdate,inserttime,|,outdent,indent,|,sub,sup,|,charmap",
      theme_advanced_statusbar_location : "bottom",
      theme_advanced_resizing : true,
      language : "{$WEBSITE_LANGUAGE}",
      remove_script_host : false,
      document_base_url : "{$WEBSITE_URL}",
      entity_encoding : "raw",
      height : "300px",
      content_css : "%PATH_CSS%/core/tinymce{$_compress_files_suffix_}.css"
    });

    candy.system.countCharLength('#input-teaser');
  });

  $('#input-teaser').bind('keyup', function() {
    candy.system.countCharLength(this);
  });

  $('p.error').tipTip({ maxWidth: "auto" });
</script>