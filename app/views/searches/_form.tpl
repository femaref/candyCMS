<form method='post'>
  <h1>{$lang_search}</h1>
  <p {if isset($error_search)}class="error"{/if}>
    <label for='search'>{$lang_terms}</label>
    <input type="search" name="id" autofocus required />
  </p>
  <p class="center">
    <input type="submit" value="{$lang_search}" />
  </p>
</form>