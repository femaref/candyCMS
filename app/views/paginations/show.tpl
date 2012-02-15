{strip}
  <div class='pagination pagination-centered clearfix'>
    <a href="{$_action_url_}/page/{$smarty.get.page + 1}" style='display:none'></a>
    <ul>
      {section pages start=1 loop=$page_last+1 step=1}
        <li{if $smarty.get.page == $smarty.section.pages.index && $page_last > 1} class='active'{/if}>
          <a href='{$_action_url_}/page/{$smarty.section.pages.index}'>
            {$smarty.section.pages.index}
          </a>
        </li>
      {/section}
    </ul>
  </div>
{/strip}