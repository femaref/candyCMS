{if !$c}
  <div class='error' id='js-error' title='{$lang_missing_entry}' onclick="hideDiv('js-error')">
    <p>{$lang_missing_entry}</p>
  </div>
{else}
  <div id='c{$c.id}' class='element'>
    {if $AJAX_REQUEST == false}
      <div class='header'>
        <h1>
          {$c.title}
          {if $USER_RIGHT >= 3}
            <a href='/content/{$c.id}/update'>
              <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang_update}'
                   title='{$lang_update}' />
            </a>
          {/if}
        </h1>
        <h4>
          {$lang_last_update}: {$c.datetime} {$lang_by}
          <a href='/user/{$c.author_id}/{$c.encoded_full_name}'>{$c.full_name}</a>
        </h4>
      </div>
    {/if}
    {$c.content}
    {if $AJAX_REQUEST == false}
      <div class='footer'>
        <div class="share">
          {$lang_share}:
          <a href='http://www.facebook.com/share.php?u={$c.url}&amp;t={$c.encoded_title}'
             class='js-tooltip' title='http://www.facebook.com'>
            <img src='%PATH_IMAGES%/spacer.png' class="icon-facebook" alt='Facebook' width='16' height='16' />
          </a>
          <a href='http://twitter.com/share?text={$c.title}&url={$c.url}'
             class='js-tooltip' title='http://www.twitter.com'>
            <img src='%PATH_IMAGES%/spacer.png' class="icon-twitter" alt='Twitter' width='16' height='16' />
          </a>
          <a href='http://del.icio.us/post?url={$c.url}&amp;title={$c.encoded_title}'
             class='js-tooltip' title='http://del.icio.us'>
            <img src='%PATH_IMAGES%/spacer.png' class="icon-delicious" alt='del.icio.us' width='16' height='16' />
          </a>
          <a href='http://technorati.com/cosmos/search.html?url={$c.url}'
             class='js-tooltip' title='http://technorati.com'>
            <img src='%PATH_IMAGES%/spacer.png' class="icon-technorati" alt='Technorati' width='16' height='16' />
          </a>
          <a href='http://digg.com/submit?phase=2&amp;url={$c.url}&amp;title={$c.encoded_title}'
             class='js-tooltip' title='http://digg.com'>
            <img src='%PATH_IMAGES%/spacer.png' class="icon-digg" alt='Digg' width='16' height='16' />
          </a>
          <a href='http://www.mister-wong.de/index.php?action=addurl&amp;bm_url={$c.url}&amp;bm_description={$c.encoded_title}'
             class='js-tooltip' title='http://www.mister-wong.de'>
            <img src='%PATH_IMAGES%/spacer.png' class="icon-mrwong" alt='MrWong' width='16' height='16' />
          </a>
        </div>
      </div>
      <div class="facebook_like">
        <fb:like href="{$c.url_clean}" ref="{$c.id}" width="674" show_faces="false"></fb:like>
      </div>
    {/if}
  </div>
{/if}