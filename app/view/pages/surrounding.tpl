<p>
  {if $page_previous !== ''}
    <a href='/{$_action_url_}/page/{$page_previous}'>&laquo; {$lang_previous_entries}</a>&nbsp;&nbsp;
  {/if}
  {if $rss_section !== ''}
    <a href='/RSS/{$rss_section}'><img src='%PATH_IMAGES%/spacer.gif' class="icon-rss" alt='{$lang_rss_feed}' /></a>
  {/if}
  {if $page_next !== '' && $page_count > $page_limit}
    &nbsp;&nbsp;<a href='/{$_action_url_}/page/{$page_next}'>{$lang_next_entries} &raquo;</a>
  {/if}
</p>