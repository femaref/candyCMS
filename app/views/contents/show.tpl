{if !$c}
  <div class='error' id='js-error' title='{$lang_missing_entry}'>
    <p>{$lang_missing_entry}</p>
  </div>
{else}
  <section id="content">
    <article class="contents">
      <header>
        <h1>
          {$c.title}
          {if $USER_RIGHT >= 3}
            <a href='/content/{$c.id}/update'>
              <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang_update}' width="16" height="16" title='{$lang_update}' />
            </a>
          {/if}
        </h1>
        <p>
          <time datetime="{$c.date_w3c}">
            {$lang_last_update}: {$c.datetime}
          </time>
          {$lang_by}
          <a href='/user/{$c.author_id}/{$c.encoded_full_name}'>{$c.full_name}</a>
        </p>
      </header>
      {if $c.teaser !== ''}
        <summary>{$c.teaser}</summary>
      {/if}
      {$c.content}
      <footer>
        <div class="share">
          {$lang_share}:
          <a href='http://www.facebook.com/share.php?u={$c.url}&amp;t={$c.encoded_title}'
             title='http://www.facebook.com'>
            <img src='%PATH_IMAGES%/spacer.png' class="icon-facebook" alt='Facebook' width='16' height='16' />
          </a>
          <a href='http://twitter.com/share?text={$c.title}&url={$c.url}'
             title='http://www.twitter.com'>
            <img src='%PATH_IMAGES%/spacer.png' class="icon-twitter" alt='Twitter' width='16' height='16' />
          </a>
          <a href='http://del.icio.us/post?url={$c.url}&amp;title={$c.encoded_title}'
             title='http://del.icio.us'>
            <img src='%PATH_IMAGES%/spacer.png' class="icon-delicious" alt='del.icio.us' width='16' height='16' />
          </a>
          <a href='http://technorati.com/cosmos/search.html?url={$c.url}'
             title='http://technorati.com'>
            <img src='%PATH_IMAGES%/spacer.png' class="icon-technorati" alt='Technorati' width='16' height='16' />
          </a>
          <a href='http://digg.com/submit?phase=2&amp;url={$c.url}&amp;title={$c.encoded_title}'
             title='http://digg.com'>
            <img src='%PATH_IMAGES%/spacer.png' class="icon-digg" alt='Digg' width='16' height='16' />
          </a>
          <a href='http://www.mister-wong.de/index.php?action=addurl&amp;bm_url={$c.url}&amp;bm_description={$c.encoded_title}'
             title='http://www.mister-wong.de'>
            <img src='%PATH_IMAGES%/spacer.png' class="icon-mrwong" alt='MrWong' width='16' height='16' />
          </a>
        </div>
        {if $FACEBOOK_APP_ID && $_facebook_plugin_ == true}
          <div class="facebook_like">
            <fb:like href="{$c.url_clean}" ref="{$c.id}" width="674" show_faces="false" send="true"></fb:like>
          </div>
        {/if}
      </footer>
    </article>
  </section>
  <script src='%PATH_PUBLIC%/js/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
  <script src='%PATH_PUBLIC%/js/core/jquery.capty{$_compress_files_suffix_}.js' type='text/javascript'></script>
  <script type="text/javascript">
    $(document).ready(function(){
      $(".js-fancybox").fancybox();
      $('.js-image').capty();

      if($('.js-media')) {
        $('.js-media').each(function(e) {
          $(this).parent().load(this.href);
        });
      };
    });
  </script>
{/if}