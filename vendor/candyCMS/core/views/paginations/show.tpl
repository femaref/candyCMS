{strip}
<div class='pagination-centered clearfix js-pagination'>
  <div class='pagination'>
    <a href="{$_PAGE.controller}/page/{$_REQUEST.page + 1}" style='display:none'></a>
    <ul>
      {section pages start=1 loop=$_PAGE.last+1 step=1}
        <li{if $_REQUEST.page == $smarty.section.pages.index && $_PAGE.last > 1} class='active'{/if}>
          <a href='{$_PAGE.controller}/page/{$smarty.section.pages.index}'>
            {$smarty.section.pages.index}
          </a>
        </li>
      {/section}
    </ul>
  </div>
</div>
{/strip}