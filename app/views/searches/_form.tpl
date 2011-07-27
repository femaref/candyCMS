<form method='post'>
  <h1>{$lang_search}</h1>
  <p {if isset($error_search)}class="error" title="{$error_search}"{/if}>
    <label for='id'>{$lang_terms} <span title="{$lang_required}">*</span></label>
    <input type="search" name="id" autofocus required />
  </p>
  <p class="center">
    <input type="submit" value="{$lang_search}" />
  </p>
</form>