{if $USER_RIGHT >= 3}
  <p class="center">
    <a href='/blog/create'>
      <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='' width="16" height="16" />
      {$lang_create_entry_headline}
    </a>
  </p>
{/if}
{if !$blog}
  <div class='error' id='js-error' title='{$lang_no_entries}'>
    <p>{$lang_no_entries}</p>
  </div>
{else}
  <section id="blog">
    {foreach $blog as $b}
      {if !$b.id}
        <div class='error' id='js-error' title='{$lang_missing_entry}'>
          <p>{$lang_missing_entry}</p>
        </div>
      {else}
        <article class="blogs">
          <header>
            <h2>
              {if $b.published == false}
                {$lang_not_published}
              {/if}
              <a href='/blog/{$b.id}/{$b.encoded_title}'>{$b.title}</a>
              {if $USER_RIGHT >= 3}
                <a href='/blog/{$b.id}/update'>
                  <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang_update}'
                       title='{$lang_update}' width="16" height="16" />
                </a>
              {/if}
            </h2>
            <p>
              <time datetime="{$b.date_w3c}">
                {$b.datetime}
                {if $b.date_modified != ''}
                  - {$lang_last_update}: {$b.date_modified}
                {/if}
              </time>
              {$lang_by}
              <a href='/user/{$b.author_id}/{$b.encoded_full_name}'>{$b.full_name}</a>
            </p>
          </header>
          {if $b.teaser !== ''}
            <summary>{$b.teaser}</summary>
          {/if}
          {$b.content}
          <footer>
            {if $b.tags[0] !== ''}
              <div class="tags">
                {$lang_tags}:
                {foreach from=$b.tags item=t name=tags}
                  <a title='{$lang_tags_info}: {$t}' href='/blog/{$t}'>{$t}</a>
                {/foreach}
              </div>
            {/if}
            <div class="share">
              {$lang_share}:
              <a href='http://www.facebook.com/share.php?u={$b.url}&amp;t={$b.encoded_title}'
                 title='http://www.facebook.com'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-facebook" alt='Facebook' width='16' height='16' />
              </a>
              <a href='http://twitter.com/share?text={$b.title}&url={$b.url}'
                 title='http://www.twitter.com'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-twitter" alt='Twitter' width='16' height='16' />
              </a>
              <a href='http://del.icio.us/post?url={$b.url}&amp;title={$b.encoded_title}'
                 title='http://del.icio.us'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-delicious" alt='del.icio.us' width='16' height='16' />
              </a>
              <a href='http://technorati.com/cosmos/search.html?url={$b.url}'
                 title='http://technorati.com'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-technorati" alt='Technorati' width='16' height='16' />
              </a>
              <a href='http://digg.com/submit?phase=2&amp;url={$b.url}&amp;title={$b.encoded_title}'
                 title='http://digg.com'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-digg" alt='Digg' width='16' height='16' />
              </a>
              <a href='http://www.mister-wong.de/index.php?action=addurl&amp;bm_url={$b.url}&amp;bm_description={$b.encoded_title}'
                 title='http://www.mister-wong.de'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-mrwong" alt='MrWong' width='16' height='16' />
              </a>
            </div>
            <div class="comments">
              <a href='/blog/{$b.id}/{$b.encoded_title}#comments'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-comments" alt='' width='16' height='16' />
                {$b.comment_sum} {$lang_comments}
              </a>
            </div>
            {if $FACEBOOK_APP_ID && $_facebook_plugin_ == true}
              <div class="facebook_like">
                <fb:like href="{$b.url_clean}" ref="{$b.id}" width="674" show_faces="false" send="true"></fb:like>
              </div>
            {/if}
          </footer>
        </article>
      {/if}
    {/foreach}
  {/if}
</section>
{$_blog_footer_}
<script src='%PATH_PUBLIC%/js/core/mediaelement{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script src='%PATH_PUBLIC%/js/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script src='%PATH_PUBLIC%/js/core/jquery.lazyload{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script src='%PATH_PUBLIC%/js/core/jquery.capty{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script type="text/javascript">
  $(document).ready(function(){
    $('video,audio').mediaelementplayer({ features: ['playpause','progress','current','duration','volume','fullscreen'] });
    $(".js-fancybox").fancybox();
    $(".image img").lazyload({ effect : "fadeIn" });
    $('.js-image').capty();

    if($('.js-toggle-headline')) {
      $('.js-toggle-headline').click(function(){
        $(this).next().toggle();
      });
    };
  });
</script>