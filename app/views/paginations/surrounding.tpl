{strip}
  <ul class='pager clearfix'>
    {if $_PAGE.previous}
      <li class='previous'>
        <a href='/{$_REQUEST.controller}/page/{$_PAGE.previous}' rel='prev'>&larr; {$lang.pages.previous}</a>
      </li>
    {/if}
    {if $_PAGE.next && $_PAGE.entries > $_PAGE.limit}
      <li class='next'>
        <a href='/{$_REQUEST.controller}/page/{$_PAGE.next}' rel='next'>{$lang.pages.next} &rarr;</a>
      </li>
    {/if}
  </ul>
  <p class='center'>
    {if $_PAGE.controller}
      <a href='/rss/{$_PAGE.controller}'>
        <img src='%PATH_IMAGES%/candy.global/spacer.png' class='icon-rss js-tooltip'
            title='{$lang.global.rss}' alt='{$lang.global.rss}' width='16' height='16' />
      </a>
    {/if}
  </p>
{/strip}