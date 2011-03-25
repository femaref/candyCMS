<script type="text/javascript">
  window.addEvent('domready', function() {
    var sValue = document.id('teaser').value;
    var iLength = sValue.length;
    var iRemaining = 140 - iLength;
    document.id('js-chars').set('html', iRemaining);

    document.id('teaser').addEvent('keyup', function() {
      var sValue = $('teaser').value;
      var iLength = sValue.length;
      var iRemaining = 140 - iLength;
      document.id('js-chars').set('html', iRemaining);
    });
  });
</script>
<script language='javascript' type='text/javascript'
  src='%PATH_PUBLIC%/lib/tiny_mce/tiny_mce.js'></script>
<script language='javascript' type='text/javascript'>
  tinyMCE.init({
    mode : "textareas",
    theme : "advanced",
    entity_encoding : "raw",
    content_css : "%PATH_CSS%/tinymce{$_compress_files_suffix_}.css",
    plugins : "autosave,safari,pagebreak,style,advimage,advlink,emotions,inlinepopups,insertdatetime,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
    theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,cut,copy,paste,pastetext,|,search,replace,|,fullscreen",
    theme_advanced_buttons2 : "styleselect,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor",
    theme_advanced_buttons3 : "hr,|,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,|,outdent,indent,|,pagebreak,|,charmap,emotions,media,|,print",
    theme_advanced_statusbar_location : "bottom",
    theme_advanced_resizing : true,
    language : "{$_language_}",
    relative_urls : false,
    remove_script_host : false,
    document_base_url : "{$WEBSITE_URL}"
  });
</script>
<form method='post' action='{$_action_url_}' class="form blog">
  <table>
    <tr>
      <th colspan='2'>
        <h1>{$lang_headline}</h1>
      </th>
    </tr>
    <tr class='row1{if $error_title} error{/if}'>
      <td class='td_left'>
        <label for='title'>{$lang_title}</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='title' value='{$title}' type='text' id='title' />
          {if $error_title}
            <div class="description">{$error_title}</div>
          {/if}
        </div>
      </td>
    </tr>
    <tr class='row2'>
      <td class='td_left'>
        <label for='tags'>{$lang_tags}</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='tags' value='{$tags}' type='text' id='tags' />
          <div class='description'>{$lang_create_tag_info}</div>
        </div>
      </td>
    </tr>
    <tr class='row1'>
      <td class='td_left'>
        <label for='teaser'>
          {$lang_teaser}
          <span id="js-chars">140</span>
        </label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='teaser' value='{$teaser}' type='text' id='teaser' />
          <div class='description'>{$lang_create_teaser_info}</div>
        </div>
      </td>
    </tr>
    <tr class='row2{if $error_content} error{/if}'>
      <td class='td_left'>
        <label for='content'>{$lang_content}</label>
      </td>
      <td class='td_right'>
        <div class="textarea">
          <textarea name='content'
            id='content' rows='16' cols='50'>{$content}</textarea>
          {if $error_content}
            <div class="description">{$error_content}</div>
          {else}
            <div class='description'>
              <img src="%PATH_IMAGES%/spacer.png" class="icon-redirect" alt="" />
              <a href='/Help/BB-Code' target='_blank'>{$lang_bb_help}</a>
            </div>
          {/if}
        </div>
      </td>
    </tr>
    <tr class='row1'>
      <td class='td_left'>
        <label for='published'>{$lang_published}</label>
      </td>
      <td class='td_right'>
        <div class="checkbox">
          <input name='published' value='1' type='checkbox'
                 id='published' {if $published == true}checked='checked'{/if} />
        </div>
      </td>
    </tr>
    {if $smarty.get.action == 'update'}
      <tr class='row2'>
        <td class='td_left'>
          <label for='show_update'>{$lang_update_show}</label>
        </td>
        <td class='td_right'>
          <div class="checkbox">
            <input type='checkbox' id='show_update'
                   name='show_update' value='1' />
          </div>
        </td>
      </tr>
    {/if}
  </table>
  <div class="submit">
    <input type='submit' value='{$lang_submit}' />
  </div>
	{if $smarty.get.action == 'update'}
      <div class="button">
        <input type='reset' value='{$lang_reset}' />
      </div>
      <div class="cancel">
        <input type='button' value='{$lang_destroy_entry}'
           onclick="confirmDelete('/blog/{$_request_id_}/destroy')" />
      </div>
	{/if}
  <input type='hidden' value='{$_request_id_}' name='id' />
  <input type='hidden' value='{$author_id}' name='author_id' />
  <input type='hidden' value='formdata' name='{$_formdata_}' />
</form>