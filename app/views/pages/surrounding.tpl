<p>
  {if $_page_previous_}
    <a href='/{$_action_url_}/page/{$_page_previous_}' class="js-prev" rel="prev">&laquo; {$lang_previous_entries}</a>&nbsp;&nbsp;
  {/if}
  {if $_rss_section_}
    <a href='/rss/{$_rss_section_}'>
      <img src='%PATH_IMAGES%/spacer.png' class="icon-rss" alt='{$lang_rss_feed}' />
    </a>
  {/if}
  {if $_page_next_ && $_page_count_ > $_page_limit_}
    &nbsp;&nbsp;<a href='/{$_action_url_}/page/{$_page_next_}' class="js-next" rel="next">{$lang_next_entries} &raquo;</a>
  {/if}
</p>