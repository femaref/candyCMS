{strip}
  <form method='post' class='form-horizontal'>
    <div class='page-header'>
      <h1>{$lang.global.search}</h1>
    </div>
    <div class='control-group'>
      <label for='input-search' class='control-label'>
        {$lang.searches.label.terms} <span title='{$lang.global.required}'>*</span>
      </label>
      <div class='controls'>
        <input type='search' class='span4 focused' name='search'
               id='input-search' autofocus required />
        <input type='submit' name='submit' class='btn btn-primary'
               value='{$lang.global.search}' data-theme='b' />
      </div>
    </div>
  </form>
{/strip}