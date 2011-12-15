{if !$content}
  <div class='error' title='{$lang.error.missing.entry}'>
    <h4>{$lang.error.missing.entry}</h4>
  </div>
{else}
  {foreach $content as $c}
    <article class="contents">
      <header>
        <h1>
          {$c.title}
          {if $USER_RIGHT >= 3}
            <a href='/content/{$c.id}/update'>
              <img src='%PATH_IMAGES%/spacer.png' class="icon-update"
                   alt='{$lang.global.update.update}' width="16" height="16" title='{$lang.global.update.update}' />
            </a>
          {/if}
        </h1>
        <p>
          <time datetime="{$c.date_w3c}">
            {$lang.global.last_update}: {$c.datetime}
          </time>
          {$lang.global.by}
          <a href='/user/{$c.author_id}/{$c.encoded_full_name}'>{$c.full_name}</a>
        </p>
      </header>
      {if $c.teaser !== ''}
        <p class="summary">{$c.teaser}</p>
      {/if}
      {$c.content}
      <footer>
        <div class="share">
          {$lang.global.share}:
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
        {if $_facebook_plugin_ == true}
          <div class="facebook_like">
            <fb:like href="{$c.url_clean}" ref="{$c.id}" width="674" show_faces="false" send="true"></fb:like>
          </div>
        {/if}
      </footer>
    </article>
    <script src='%PATH_JS%/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
    <script src='%PATH_JS%/core/jquery.capty{$_compress_files_suffix_}.js' type='text/javascript'></script>
    <script type="text/javascript">
      $(document).ready(function(){
        $(".js-fancybox").fancybox();
        $('.js-image').capty({ height: 35 });
      });

      $('.js-media').each(function(e) {
        var $this = $(this);
        $.getJSON(this.title, function(data) {
          $this.html(data['html']);
        });
      });
    </script>
  {/foreach}
{/if}