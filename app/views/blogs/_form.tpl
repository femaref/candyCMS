{strip}
  <div class='page-header'>
    <h1>
      {if $smarty.get.action == 'create'}
        {$lang.blog.title.create}
      {else}
        {$lang.blog.title.update|replace:'%p':$title}
      {/if}
    </h1>
  </div>
  <form method='post' action='/blog/{$smarty.get.action}' class='form-horizontal'>
    <div class='control-group{if isset($error.title)} alert alert-error{/if}'>
      <label for='input-title' class='control-label'>
        {$lang.global.title} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        <input name='title' value="{$title}" type='text' id='input-title'
              class='span4 required' required />
        <span class='help-inline'>
          {if isset($error.title)}
            {$error.title}
          {/if}
        </span>
      </div>
    </div>
    <div class='control-group'>
      <label for='input-teaser' class='control-label'>
        {$lang.global.teaser}
      </label>
      <div class='controls'>
        <input name='teaser' value="{$teaser}" type='text' class='span4'
              id='input-teaser' />
        <span class='help-inline'></span>
        <p class='help-block'>
          {$lang.blog.info.teaser}
        </p>
      </div>
    </div>
    <div class='control-group'>
      <label for='input-tags' class='control-label'>
        {$lang.global.tags.tags}
      </label>
      <div class='controls'>
        <input type='text' name='tags' id='input-tags'
              data-provide='typeahead' value="{$tags}"
              data-source='{$_tags_}' data-items='8'
              class='span4 required' autocomplete='off' required />
        <p class='help-block'>
          {$lang.blog.info.tag}
        </p>
      </div>
    </div>
    <div class='control-group'>
      <label for='input-keywords' class='control-label'>
        {$lang.global.keywords}
      </label>
      <div class='controls'>
        <input name='keywords' value="{$keywords}" type='text' id='input-keywords'
              title='{$lang.blog.info.keywords}' class='span4' />
        <p class='help-block'>
          {$lang.blog.info.keywords}
        </p>
      </div>
    </div>
    <div class='control-group{if isset($error.content)} alert alert-error{/if}'>
      <label for='input-content' class='control-label'>
        {$lang.global.content} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        <textarea name='content' class='js-tinymce required'
                  id='input-content' required>{$content}</textarea>
        {if isset($error.content)}<span class='help-inline'>{$error.content}</span>{/if}
      </div>
    </div>
    <div class='control-group'>
      <label for='input-published' class='control-label'>
        {$lang.global.published}
      </label>
      <div class='controls'>
        <input name='published' value='1' type='checkbox' class='checkbox'
              id='input-published' {if $published == true}checked{/if} />
      </div>
    </div>
    {if $smarty.get.action == 'update'}
      <div class='control-group'>
        <label for='input-update_date' class='control-label'>
          {$lang.blog.label.date}
        </label>
        <div class='controls'>
            <input name='update_date' value='1' type='checkbox'
                  id='input-update_date' class='checkbox' />
        </div>
      </div>
      <div class='control-group'>
        <label for='input-show_update' class='control-label'>
          {$lang.global.update.show}
        </label>
        <div class='controls'>
            <input type='checkbox' class='checkbox' name='show_update' value='1'
                  id='input-show_update' {if $date_modified > 0}checked{/if} />
        </div>
      </div>
    {/if}
    <div class='form-actions'>
      {if isset($author_id)}
        <input type='hidden' value='{$author_id}' name='author_id' />
      {/if}
      <input type='hidden' value='formdata' name='{$smarty.get.action}_blog' />
      <input type='submit' class='btn btn-primary' value='{if $smarty.get.action == 'create'}{$lang.global.create.create}{else}{$lang.global.update.update}{/if}' />
      {if $smarty.get.action == 'update'}
        <input type='button' class='btn btn-danger' value='{$lang.blog.title.destroy}' onclick='confirmDestroy('/blog/{$_request_id_}/destroy')' />
        <input type='reset' class='btn' value='{$lang.global.reset}' />
        <input type='hidden' value='{$_request_id_}' name='id' />
        <input type='hidden' value='{$date}' name='date' />
      {/if}
    </div>
  </form>
  <script type='text/javascript' src='%PATH_JS%/core/jquery.typeahead{$_compress_files_suffix_}.js'></script>
  <script type='text/javascript' src='/lib/tiny_mce/jquery.tinymce.js'></script>
  <script type='text/javascript'>
    $(document).ready(function(){
      $('textarea.js-tinymce').tinymce({
        script_url : '/lib/tiny_mce/tiny_mce.js',
        theme : 'advanced',
        plugins : 'autosave,safari,style,advimage,advlink,inlinepopups,insertdatetime,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras',
        theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,cut,copy,paste,pastetext,|,search,replace,|,fullscreen',
        theme_advanced_buttons2 : 'styleselect,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor',
        theme_advanced_buttons3 : 'hr,|,link,unlink,anchor,|,image,|,cleanup,removeformat,|,code,|,insertdate,inserttime,|,outdent,indent,|,sub,sup,|,charmap',
        theme_advanced_statusbar_location : 'bottom',
        theme_advanced_resizing : true,
        language : '{$WEBSITE_LANGUAGE}',
        remove_script_host : false,
        document_base_url : '{$WEBSITE_URL}',
        entity_encoding : 'raw',
        height : '300px',
        content_css : '%PATH_CSS%/core/tinymce{$_compress_files_suffix_}.css'
      });
    });

    $('#input-title').bind('keyup', function() {
      countCharLength(this, 128);
    });

    $('#input-teaser').bind('keyup', function() {
      countCharLength(this, 180);
    });
  </script>
{/strip}