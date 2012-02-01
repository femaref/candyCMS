<div class="pages">
  <a href="{$_action_url_}/page/{$smarty.get.page + 1}"></a>
  {section pages start=1 loop=$page_last+1 step=1}
    {if $smarty.get.page == $smarty.section.pages.index && $page_last > 1}
      <span>{$smarty.section.pages.index}</span>
    {elseif $page_last > 1}
      <a href="{$_action_url_}/page/{$smarty.section.pages.index}">
        {$smarty.section.pages.index}
      </a>
    {/if}
  {/section}
</div>