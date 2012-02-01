<div class="pagination">
  <ul>
    {if $_page_previous_}
      <li class="prev">
        <a href='/{$smarty.get.section}/page/{$_page_previous_}' rel="prev">&larr; {$lang.pages.previous}</a>
      </li>
    {/if}
    {if $_page_next_ && $_page_entries_ > $_page_limit_}
      <li class="next">
        <a href='/{$smarty.get.section}/page/{$_page_next_}' rel="next">{$lang.pages.next} &rarr;</a>
      </li>
    {/if}
  </ul>
</div>

{if $_rss_section_}
  <a href='/rss/{$_rss_section_}'>
    <img src='%PATH_IMAGES%/spacer.png' class="icon-rss" alt='{$lang.global.rss}' width="16" height="16" />
  </a>
{/if}
