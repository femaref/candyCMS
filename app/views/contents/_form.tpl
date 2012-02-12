<div class='page-header'>
  <h1>
    {if $smarty.get.action == 'create'}
      {$lang.content.title.create}
    {else}
      {$lang.content.title.update|replace:'%p':$title}
    {/if}
  </h1>
</div>
<form method='post' action='/content/{$smarty.get.action}' class='form-horizontal'>
  <fieldset>
    <div class='control-group{if isset($error.title)} error{/if}'>
      <label for='input-title' class='control-label'>
        {$lang.global.title} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        <input type='text' name='title' class='span4 required'
               title='{$lang.global.title}' value='{$title}' id='input-title' autofocus required />
        {if isset($error.title)}
          <span class='help-inline'>{$error.title}</span>
        {/if}
      </div>
    </div>
    <div class='control-group'>
      <label for='input-teaser'>
        {$lang.global.teaser}
      </label>
      <div class='controls'>
        <input name='teaser' value='{$teaser}' type='text' placeholder='{$lang.content.info.teaser}'
               title='{$lang.content.info.teaser}' id='input-teaser' />
        <span id='js-count_chars'></span>
        <p class='help-block'>
          {$lang.blog.info.teaser}
        </p>
      </div>
    </div>
    <div class='clearfix'>
      <label for='input-keywords'>{$lang.global.keywords}</label>
      <div class='input'>
        <input name='keywords' value='{$keywords}' type='text' placeholder='{$lang.content.info.keywords}' title='{$lang.content.info.keywords}' id='input-keywords' />
      </div>
    </div>
    <div class='clearfix{if isset($error.content)} error{/if}'>
      <label for='input-content'>{$lang.global.content} <span title='{$lang.global.required}'>*</span></label>
      <div class='input'>
        <textarea name='content' title='{$lang.global.content}' class='js-tinymce' id='input-content'>{$content}</textarea>
        {if isset($error.content)}<span class='help-inline'>{$error.content}</span>{/if}
      </div>
    </div>
    <div class='clearfix'>
      <label for='input-published'>{$lang.global.published}</label>
      <div class='input'>
        <ul class='inputs-list'>
          <li>
            <label>
              <input name='published' value='1' type='checkbox' id='input-published' {if $published == true}checked{/if} />
            </label>
          </li>
        </ul>
      </div>
    </div>
    <div class='actions'>
      <input type='submit' class='btn primary' value="{if $smarty.get.action == 'create'}{$lang.global.create.create}{else}{$lang.global.update.update}{/if}" />
      <input type='hidden' value='formdata' name='{$smarty.get.action}_content' />
      {if $smarty.get.action == 'update'}
        <input type='hidden' value='{$_request_id_}' name='id' />
        <input type='reset' class='btn' value='{$lang.global.reset}' />
        <input type='button' class='btn' value='{$lang.content.title.destroy}'
               onclick="candy.system.confirmDestroy('/content/{$_request_id_}/destroy')" />
      {/if}
    </div>
  </fieldset>
</form>
<script type='text/javascript' src='/lib/tiny_mce/jquery.tinymce.js'></script>
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
</script>