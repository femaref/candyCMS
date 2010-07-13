<p>
  {section name=pages start=1 loop=$page+1 step=1}
    {if $smarty.get.page == $smarty.section.pages.index && $page > 1}
      <span class='currentpagelink'>{$smarty.section.pages.index}</span>
    {elseif $page > 1}
      <a onclick="reloadPage('/{$URL}/page/{$smarty.section.pages.index}', '{$ROOT}')" href='#comments' class='pagelink'>{$smarty.section.pages.index}</a>
    {/if}
  {/section}
</p>
