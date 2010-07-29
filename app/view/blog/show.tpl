{if $UR > 3}
  <p>
    <a href='/Blog/create'>
      <img src='%PATH_IMAGES%/spacer.gif' class="icon-create" alt='' />
      {$lang_create_entry_headline}
    </a>
  </p>
{/if}
{if !$blog}
  <div class='error' id='error' title='{$lang_no_entries}' onclick="hideDiv('error')">
    <p>{$lang_no_entries}</p>
  </div>
{else}
  {foreach from=$blog item=b}
    {if !$b.id}
      <div class='error' id='error' title='{$lang_missing_entry}' onclick="hideDiv('error')">
        <p>{$lang_missing_entry}</p>
      </div>
    {else}
      <div id='b{$b.id}' class='element'>
        <div class='header'>
          <h2>
            {if $b.published == false}
              {$lang_not_published}
            {/if}
            <a href='/Blog/{$b.id}/{$b.eTitle}'>{$b.title}</a>
            {if $UR > 3}
              <a href='/Blog/update/{$b.id}'>
                <img src='%PATH_IMAGES%/spacer.gif' class="icon-update" alt='{$lang_update}'
                     title='{$lang_update}' />
              </a>
            {/if}
          </h2>
          <div class='date'>
            {$b.date} {$lang_by}
            <a href='/User/{$b.authorID}'>{$b.name} {$b.surname}</a>
            {if $b.date_modified != '01.01.1970 - 01:00'}
              - {$lang_last_update}: {$b.date_modified}
            {/if}
          </div>
        </div>
        {$b.content}
        <div class='footer'>
          {if $b.tags_sum > 0}
            {$lang_tags}:
            {foreach from=$b.tags item=t name=tags}
              <a class='tooltip' title='{$lang_tags_info}::{$t}' href='/Blog/tag/{$t}'>{$t}</a>
              {if $smarty.foreach.tags.iteration < $b.tags_sum}, {/if}
            {/foreach}
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          {/if}
          {$lang_share}:
          <a href='http://www.facebook.com/share.php?u={$URL}/Blog/{$b.id}/{$b.eTitle}&amp;t={$b.eTitle}'
             class='tooltip' title='{$lang_add_bookmark}::http://www.facebook.com'>
            <img src='%PATH_IMAGES%/spacer.gif' class="icon-facebook" alt='Facebook' width='16' height='16' />
          </a>
          <a href='http://del.icio.us/post?url={$URL}/Blog/{$b.id}/{$b.eTitle}&amp;title={$b.eTitle}'
             class='tooltip' title='{$lang_add_bookmark}::http://del.icio.us'>
            <img src='%PATH_IMAGES%/spacer.gif' class="icon-delicious" alt='del.icio.us' width='16' height='16' />
          </a>
          <a href='http://technorati.com/cosmos/search.html?url={$URL}/Blog/{$b.id}/{$b.eTitle}'
             class='tooltip' title='{$lang_add_bookmark}::http://technorati.com'>
            <img src='%PATH_IMAGES%/spacer.gif' class="icon-technorati" alt='Technorati' width='16' height='16' />
          </a>
          <a href='http://digg.com/submit?phase=2&amp;url={$URL}/Blog/{$b.id}/{$b.eTitle}&amp;title={$b.eTitle}'
             class='tooltip' title='{$lang_add_bookmark}::http://digg.com'>
            <img src='%PATH_IMAGES%/spacer.gif' class="icon-digg" alt='Digg' width='16' height='16' />
          </a>
          <a href='http://www.mister-wong.de/index.php?action=addurl&amp;bm_url={$URL}/Blog/{$b.id}/{$b.eTitle}&amp;bm_description={$b.eTitle}'
             class='tooltip' title='{$lang_add_bookmark}::http://www.mister-wong.de'>
            <img src='%PATH_IMAGES%/spacer.gif' class="icon-mrwong" alt='MrWong' width='16' height='16' />
          </a>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <a href='/Blog/{$b.id}/{$b.eTitle}#comments'>
            <img src='%PATH_IMAGES%/spacer.gif' class="icon-comments" alt='' /> {$b.comment_sum} {$lang_comments}
          </a>
        </div>
      </div>
    {/if}
  {/foreach}
  {$blogPages}
  <a name='comments'></a>
  <div id='reload'>
    {$blogComments}
  </div>
{/if}