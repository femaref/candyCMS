<form action='/calendar/{$smarty.get.action}' method='post'>
  <h1>{$lang.global.calendar}</h1>
  <p {if isset($error.title)}class="error" title="{$error.title}"{/if}>
    <label for='input-title'>{$lang.global.title} <span title="{$lang.global.required}">*</span></label>
    <input type='text' name='title' id="input-title" value='{$title}' required autofocus/>
  </p>
  <p {if isset($error.content)}class="error" title="{$error.content}"{/if}>
    <label for='input-content'>{$lang.global.description}</label>
    <input type='text' name='content' id="input-content" value='{$content}' />
  </p>
  <p {if isset($error.start_date)}class="error" title="{$error.start_date}"{/if}>
    <label for='input-start_date'>{$lang.global.date.start} <span title="{$lang.global.required}">*</span></label>
    <input type='date' name='start_date' id="input-start_date" value='{$start_date}'
           placeholder='{$lang.calendar.info.date_format}' autocomplete required
           min="{$_date_}" />
  </p>
  <p {if isset($error.end_date)}class="error" title="{$error.end_date}"{/if}>
    <label for='input-end_date'>{$lang.global.date.end}</label>
    <input type='date' name='end_date' id="input-end_date" value='{$end_date}'
           placeholder='{$lang.calendar.info.date_format}' autocomplete
           min="{$_date_}" />
  </p>
  <p class="center">
    <input type='hidden' value='formdata' name='{$smarty.get.action}_calendar' />
    <input type='submit' value='{if $smarty.get.action == 'create'}{$lang.global.create.create}{else}{$lang.global.update.update}{/if}' />
    {if $smarty.get.action == 'update'}
      <input type='button' value='{$lang.global.destroy.destroy}' onclick="candy.system.confirmDestroy('/calendar/{$_request_id_}/destroy')" />
      <input type='hidden' value='{$_request_id_}' name='id' />
      <input type='reset' value='{$lang.global.reset}' />
    {/if}
  </p>
</form>
<script type='text/javascript' src='%PATH_JS%/core/jquery.tiptip{$_compress_files_suffix_}.js'></script>
<script type="text/javascript">
  $('p.error').tipTip({ maxWidth: "auto" });
</script>