{if $USER_RIGHT > 3}
  <p>
    <a href='/Blog/create'>
      <img src='%PATH_IMAGES%/spacer.gif' class="icon-create" alt='' />
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
            <div class='date'>
              {$b.date}
            </div>
            <h2>
              {if $b.published == false}
                {$lang_not_published}
              {/if}
              <a href='/Blog/{$b.id}/{$b.eTitle}'>{$b.title}</a>
              {if $USER_RIGHT > 3}
                <a href='/Blog/{$b.id}/update'>
                  <img src='%PATH_IMAGES%/spacer.gif' class="icon-update" alt='{$lang_update}'
                       title='{$lang_update}' />
                </a>
              {/if}
            </h2>
          </div>
          {if $b.date_modified != ''}
            <span class="small">{$lang_last_update}: {$b.date_modified}</span>
          {/if}
          {if $b.teaser !== ''}
            <div class="teaser">{$b.teaser}</div>
          {/if}
          {$b.content}
          <div class='footer'>
            {$lang_share}:
            <a href='http://www.facebook.com/share.php?u={$URL}/Blog/{$b.id}/{$b.eTitle}&amp;t={$b.eTitle}'
               class='js-tooltip' title='{$lang_add_bookmark}::http://www.facebook.com'>
              <img src='%PATH_IMAGES%/spacer.gif' class="icon-facebook" alt='Facebook' width='16' height='16' />
            </a>
            <a href='http://del.icio.us/post?url={$URL}/Blog/{$b.id}/{$b.eTitle}&amp;title={$b.eTitle}'
               class='js-tooltip' title='{$lang_add_bookmark}::http://del.icio.us'>
              <img src='%PATH_IMAGES%/spacer.gif' class="icon-delicious" alt='del.icio.us' width='16' height='16' />
            </a>
            <a href='http://technorati.com/cosmos/search.html?url={$URL}/Blog/{$b.id}/{$b.eTitle}'
               class='js-tooltip' title='{$lang_add_bookmark}::http://technorati.com'>
              <img src='%PATH_IMAGES%/spacer.gif' class="icon-technorati" alt='Technorati' width='16' height='16' />
            </a>
            <a href='http://digg.com/submit?phase=2&amp;url={$URL}/Blog/{$b.id}/{$b.eTitle}&amp;title={$b.eTitle}'
               class='js-tooltip' title='{$lang_add_bookmark}::http://digg.com'>
              <img src='%PATH_IMAGES%/spacer.gif' class="icon-digg" alt='Digg' width='16' height='16' />
            </a>
            <a href='http://www.mister-wong.de/index.php?action=addurl&amp;bm_url={$URL}/Blog/{$b.id}/{$b.eTitle}&amp;bm_description={$b.eTitle}'
               class='js-tooltip' title='{$lang_add_bookmark}::http://www.mister-wong.de'>
              <img src='%PATH_IMAGES%/spacer.gif' class="icon-mrwong" alt='MrWong' width='16' height='16' />
            </a>
            <a href='/Blog/{$b.id}/{$b.eTitle}#comments' style="float:right">
              <img src='%PATH_IMAGES%/spacer.gif' class="icon-comments" alt='' /> {$b.comment_sum} {$lang_comments}
            </a>
          </div>
        </div>
      </div>
    {/if}
  {/foreach}
  {$_blog_pages_}
  <a name='comments'></a>
  {$_blog_comments_}
{/if}
<script type="text/javascript">
  var sFilesSuffix = '{$_compress_files_suffix_}';
  {literal}
    window.addEvent('domready', function() {
      new Asset.javascript('%PATH_PUBLIC%/js/core/slimbox' + sFilesSuffix + '.js');
    });

    var myAccordion = new Fx.Accordion($$('.js-toggle'), $$('.js-element'), {
      display: -1,
      alwaysHide: true
    });
  {/literal}
</script>