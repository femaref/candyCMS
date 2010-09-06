<p>
  {section name=pages start=1 loop=$page_count+1 step=1}
    {if $smarty.get.page == $smarty.section.pages.index && $page_count > 1}
      <span class='currentpagelink'>{$smarty.section.pages.index}</span>
    {elseif $page_count > 1}
      <a onclick="reloadPage('/{$_action_url_}/page/{$smarty.section.pages.index}', '{$_public_folder_}')" href="#reload" class='pagelink'>{$smarty.section.pages.index}</a>
    {/if}
  {/section}
</p>