<form method='post' class='form-horizontal'>
  <div class='page-header'>
    <h1>{$lang.global.search}</h1>
  </div>
  <div class='control-group {if isset($error.search)}error{/if}'>
    <label for='input-search' class='control-label'>
      {$lang.search.label.terms} <span title="{$lang.global.required}">*</span>
    </label>
    <div class='controls'>
      <input type="search" class="span4" name="search" id="input-search" autofocus required />
      <input type="submit" class='btn' value="{$lang.global.search}" data-theme="b" />
    </div>
  </div>
</form>