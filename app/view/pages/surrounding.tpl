<p>
  {if $previous !== ''}
    <a href='/{$URL}/page/{$previous}'>&laquo; {$lang_previous_entries}</a>&nbsp;&nbsp;
  {/if}
  {if $RSS !== ''}
    <a href='/RSS/{$RSS}'><img src='%PATH_IMAGES%/icons/rss.png' alt='{$lang_rss_feed}' /></a>
  {/if}
  {if $next !== '' && $count > $limit}
    &nbsp;&nbsp;<a href='/{$URL}/page/{$next}'>{$lang_next_entries} &raquo;</a>
  {/if}
</p>