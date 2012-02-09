<form method='post' action='/gallery/{$smarty.get.action}' class='form-horizontal'>
  <div class='page-header'>
    <h1>
      {if $smarty.get.action == 'create'}
        {$lang.gallery.albums.title.create}
      {else}
        {$lang.gallery.albums.title.update|replace:'%p':$title}
      {/if}
    </h1>
  </div>
  <div class='control-group{if isset($error.title)} error{/if}'>
    <label for='input-title' class='control-label'>
      {$lang.global.title} <span title="{$lang.global.required}">*</span>
    </label>
    <div class='controls'>
      <input name='title' value='{$title}' id="input-title" class='required span4'
             type='text' autofocus required />
    </div>
  </div>
  <div class='control-group'>
    <label for='input-content' class='control-label'>
      {$lang.global.description}
    </label>
    <div class='controls'>
      <input name='content' value='{$content}' id="input-content" type='text' class='span4' />
      <span class='help-inline' id="js-count_chars"></span>
    </div>
  </div>
  <div class="form-actions">
    <input type='submit' class='btn btn-primary'
           value="{if $smarty.get.action == 'create'}{$lang.global.create.create}{else}{$lang.global.update.update}{/if}" />
    <input type='hidden' value='formdata' name='{$smarty.get.action}_gallery' />
    {if $smarty.get.action == 'update'}
      <input type='hidden' value='{$_request_id_}' name='id' />
      <input type='button' value='{$lang.gallery.albums.title.destroy}' class='btn btn-danger'
        onclick="candy.system.confirmDestroy('/gallery/{$_request_id_}/destroy')" />
      <input type='reset' value='{$lang.global.reset}' class='btn' />
    {/if}
  </div>
</form>
<script type='text/javascript'>
  $('#input-content').bind('keyup', function() {
    candy.system.countCharLength(this);
  });
</script>