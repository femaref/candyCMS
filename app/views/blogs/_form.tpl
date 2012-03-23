{strip}
  <div class='page-header'>
    <h1>
      {if $_REQUEST.action == 'create'}
        {$lang.blogs.title.create}
      {else}
        {$lang.blogs.title.update|replace:'%p':$title}
      {/if}
    </h1>
  </div>
  <form method='post' class='form-horizontal'
        action='/{$_REQUEST.controller}/{if isset($_REQUEST.id)}{$_REQUEST.id}/{/if}{$_REQUEST.action}'>
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
          {$lang.blogs.info.teaser}
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
              data-source="{$_tags_}" data-items='8'
              class='span4' autocomplete='off' />
        <p class='help-block'>
          {$lang.blogs.info.tag}
        </p>
      </div>
    </div>
    <div class='control-group'>
      <label for='input-keywords' class='control-label'>
        {$lang.global.keywords}
      </label>
      <div class='controls'>
        <input name='keywords' value="{$keywords}" type='text' id='input-keywords'
              title='{$lang.blogs.info.keywords}' class='span4' />
        <p class='help-block'>
          {$lang.blogs.info.keywords}
        </p>
      </div>
    </div>

    <div class='control-group{if isset($error.content)} alert alert-error{/if}'>
      <label for='input-content' class='control-label'>
        {$lang.global.content} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        <textarea name='content' class='js-tinymce required'
                  id='input-content'>{$content}</textarea>
        {if isset($error.content)}
          <span class='help-inline'>{$error.content}</span>
        {/if}
      </div>
    </div>
    <div class='control-group'>
      <label for='input-language' class='control-label'>
        {$lang.global.language}
      </label>
      <div class='controls'>
        <select name='language' class='span4' id='input-language'>
          {foreach $languages as $l}
            <option value='{$l}' {if $l == $WEBSITE_LANGUAGE}selected='selected'{/if}>{$l}</option>
          {/foreach}
        </select>
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
    {if $_REQUEST.action == 'update'}
      <div class='control-group'>
        <label for='input-update_date' class='control-label'>
          {$lang.blogs.label.date}
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
      <input type='hidden' value='formdata' name='{$_REQUEST.action}_{$_REQUEST.controller}' />
      {if $_REQUEST.action == 'create'}
        <input type='submit' class='btn btn-primary' value="{$lang.global.create.create}" />
      {elseif $_REQUEST.action == 'update'}
        <input type='submit' class='btn btn-primary' value="{$lang.global.update.update}" />
          <input type='button' class='btn btn-danger' value='{$lang.blogs.title.destroy}'
                onclick="confirmDestroy('/{$_REQUEST.controller}/{$_REQUEST.id}/destroy')" />
          <input type='reset' class='btn' value='{$lang.global.reset}' />
          <input type='hidden' value='{$_REQUEST.id}' name='id' />
          <input type='hidden' value='{$date}' name='date' />
      {/if}
    </div>
  </form>
  <script type='text/javascript' src='{$_PATH.js}/core/jquery.typeahead{$_SYSTEM.compress_files_suffix}.js'></script>
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
        content_css : '{$_PATH.css}/core/tinymce{$_SYSTEM.compress_files_suffix}.css'
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