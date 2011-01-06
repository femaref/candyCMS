{if $_plugin_lazyload_}
  {$_plugin_lazyload_}
{/if}
{if $USER_RIGHT > 3}
  <p>
    <a href='/blog/create'>
      <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='' />
      {$lang_create_entry_headline}
    </a>
  </p>
{/if}
{if !$blog}
  <div class='error' id='js-error' title='{$lang_no_entries}' onclick="hideDiv('js-error')">
    <p>{$lang_no_entries}</p>
  </div>
{else}
  {foreach $blog as $b}
    {if !$b.id}
      <div class='error' id='js-error' title='{$lang_missing_entry}' onclick="hideDiv('js-error')">
        <p>{$lang_missing_entry}</p>
      </div>
    {else}
      <div id='b{$b.id}' class='element'>
        <div class="body">
          <div class='header'>
            <div class='date' title="{$b.datetime}">
              {$b.date}
            </div>
            <h2>
              {if $b.published == false}
                {$lang_not_published}
              {/if}
              <a href='/blog/{$b.id}/{$b.encoded_title}'>{$b.title}</a>
              {if $USER_RIGHT > 3}
                <a href='/blog/{$b.id}/update'>
                  <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang_update}'
                       title='{$lang_update}' />
                </a>
              {/if}
            </h2>
          </div>
          {if $b.date_modified != ''}
            <span class="small">{$lang_last_update}: {$b.date_modified}</span>
          {/if}
          {if $b.teaser !== ''}
            <p class="teaser">{$b.teaser}</p>
          {/if}
          {$b.content}
          <div class='footer'>
            {if $b.tags[0] !== ''}
              {$lang_tags}:
              {foreach from=$b.tags item=t name=tags}
                <a class='js-tooltip' title='{$lang_tags_info}::{$t}' href='/blog/{$t}'>{$t}</a>{if !$t@last}, {/if}
              {/foreach}
              <br />
            {/if}
            <div class="share">
              {$lang_share}:
              <a href='http://www.facebook.com/share.php?u={$b.url}&amp;t={$b.encoded_title}'
                 class='js-tooltip' title='{$lang_add_bookmark}::http://www.facebook.com'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-facebook" alt='Facebook' width='16' height='16' />
              </a>
              <a href='http://twitter.com/share?text={$b.title}&url={$b.url}'
                 class='js-tooltip' title='{$lang_add_bookmark}::http://www.twitter.com'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-twitter" alt='Twitter' width='16' height='16' />
              </a>
              <a href='http://del.icio.us/post?url={$b.url}&amp;title={$b.encoded_title}'
                 class='js-tooltip' title='{$lang_add_bookmark}::http://del.icio.us'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-delicious" alt='del.icio.us' width='16' height='16' />
              </a>
              <a href='http://technorati.com/cosmos/search.html?url={$b.url}'
                 class='js-tooltip' title='{$lang_add_bookmark}::http://technorati.com'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-technorati" alt='Technorati' width='16' height='16' />
              </a>
              <a href='http://digg.com/submit?phase=2&amp;url={$b.url}&amp;title={$b.encoded_title}'
                 class='js-tooltip' title='{$lang_add_bookmark}::http://digg.com'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-digg" alt='Digg' width='16' height='16' />
              </a>
              <a href='http://www.mister-wong.de/index.php?action=addurl&amp;bm_url={$b.url}&amp;bm_description={$b.encoded_title}'
                 class='js-tooltip' title='{$lang_add_bookmark}::http://www.mister-wong.de'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-mrwong" alt='MrWong' width='16' height='16' />
              </a>
            </div>
            <a href='/blog/{$b.id}/{$b.encoded_title}#comments' style="float:right">
              <img src='%PATH_IMAGES%/spacer.png' class="icon-comments" alt='' /> {$b.comment_sum} {$lang_comments}
            </a>
          </div>
          <div class="facebook_like">
            <fb:like href="{$b.url_clean}" ref="{$b.id}" width="600" show_faces="false"></fb:like>
          </div>
        </div>
      </div>
      {if $b@index == 1}
        {$_plugin_adsense_}
      {/if}
    {/if}
  {/foreach}
  {$_blog_pages_}
  <a name='comments'></a>
  {$_blog_comments_}
{/if}
<script type="text/javascript">
  window.addEvent('domready', function() {
    new Asset.javascript('%PATH_PUBLIC%/js/core/slimbox{$_compress_files_suffix_}.js');
  });

  if($$('.js-toggle')) {
    var myAccordion = new Fx.Accordion($$('.js-toggle'), $$('.js-element'), {
      display: -1,
      alwaysHide: true
    });
  }
</script>