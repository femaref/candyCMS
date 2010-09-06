{literal}
  <script language='javascript' type='text/javascript'
  src='%PATH_PUBLIC%/lib/tiny_mce/tiny_mce.js'></script>
  <script language='javascript' type='text/javascript'>
    tinyMCE.init({
      mode : "textareas",
      theme : "advanced",
      theme_advanced_resize_horizontal : "true",
      entity_encoding : "raw",
      plugins : "safari,pagebreak,style,advimage,advlink,emotions,inlinepopups,insertdatetime,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
      theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,cut,copy,paste,pastetext,|,search,replace,|,fullscreen",
      theme_advanced_buttons2 : "styleselect,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor",
      theme_advanced_buttons3 : "hr,|,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,|,outdent,indent,|,pagebreak,|,charmap,emotions,media,|,print",
      theme_advanced_statusbar_location : "bottom",
    });
  </script>
{/literal}
<form method='post' action='/Newsletter/create'>
  <table>
    <tr>
      <th colspan='2'>{$lang_headline}</th>
    </tr>
    <tr class='row1{if $error_subject} error{/if}'>
      <td class='td_left'>
        <label for='subject'>{$lang_subject}</label>
      </td>
      <td class='td_right'>
        <div class="input">
          <input name='subject' id='subject' value='{$subject}' type='text' />
          {if $error_subject}
            <div class="description">{$error_subject}</div>
          {/if}
        </div>
      </td>
    </tr>
    <tr class='row2{if $error_content} error{/if}'>
      <td class='td_left'>
        <label for='content'>{$lang_content}</label>
      </td>
      <td class='td_right'>
        <div class="textarea">
          <textarea name='content' rows='20' cols='50'
                    id='content'>{$content}</textarea>
          {if $error_content}
            <div class="description">{$error_content}</div>
          {else}
            <div class='description'>{$lang_content_info}</div>
          {/if}
        </div>
      </td>
    </tr>
  </table>
  <div class="submit">
    <input type='submit' value='{$lang_submit}' />
  </div>
  <input type='hidden' value='formdata' name='send_newsletter' />
</form>