<section class="pages">
  {if $_page_previous_}
    <a href='/{$smarty.get.section}/page/{$_page_previous_}' rel="prev">&laquo; {$lang.pages.previous}</a>&nbsp;&nbsp;
  {/if}
  {if $_rss_section_}
    <a href='/rss/{$_rss_section_}'>
      <img src='%PATH_IMAGES%/spacer.png' class="icon-rss" alt='{$lang_rss_feed}' width="16" height="16" />
    </a>
  {/if}
  {if $_page_next_ && $_page_entries_ > $_page_limit_}
    &nbsp;&nbsp;<a href='/{$smarty.get.section}/page/{$_page_next_}' rel="next">{$lang.pages.next} &raquo;</a>
  {/if}
</section>