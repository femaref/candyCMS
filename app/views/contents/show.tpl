{strip}
  {foreach $content as $c}
    <article class='contents'>
      <header class='page-header'>
        <h1>
          {$c.title}
          {if $USER_ROLE >= 3}
            <a href='/content/{$c.id}/update'>
              <img src='%PATH_IMAGES%/spacer.png'
                  class='icon-update js-tooltip'
                  alt='{$lang.global.update.update}'
                  title='{$lang.global.update.update}'
                  width='16' height='16' />
            </a>
          {/if}
        </h1>
        <p>
          {$lang.global.last_update}:
          &nbsp;
          <time datetime='{$c.date_w3c}'>
            {$c.datetime}
          </time>
          &nbsp;
          {$lang.global.by}
          &nbsp;
          <a href='/user/{$c.author_id}/{$c.encoded_full_name}' rel='author'>{$c.full_name}</a>
        </p>
      </header>
      {if $c.teaser !== ''}
        <p class='summary'>{$c.teaser}</p>
      {/if}
      {$c.content}
      <footer>
        {if $_request_id_ && (!isset($smarty.get.action) || $smarty.get.action !== 'page')}
          <hr />
          <div id='socialshareprivacy'></div>
          <script src='%PATH_JS%/core/jquery.socialshareprivacy{$_compress_files_suffix_}.js' type='text/javascript'></script>
        {/if}
      </footer>
    </article>
    <script src='%PATH_JS%/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
    <script src='%PATH_JS%/core/jquery.capty{$_compress_files_suffix_}.js' type='text/javascript'></script>
    <script type="text/javascript">
      $(document).ready(function(){
        $(".js-fancybox").fancybox();
        $('.js-image').capty({ height: 35 });

        if($('#socialshareprivacy').length > 0){
          $('#socialshareprivacy').socialSharePrivacy({
            services : {
              facebook : {
                'language' : '{$WEBSITE_LOCALE}',
                'dummy_img' : '%PATH_IMAGES%/jquery.socialshareprivacy/dummy_facebook.png'
              },
              twitter : {
                'dummy_img' : '%PATH_IMAGES%/jquery.socialshareprivacy/dummy_twitter.png'
              },
              gplus : {
                'dummy_img' : '%PATH_IMAGES%/jquery.socialshareprivacy/dummy_gplus.png',
                'display_name' : 'Google Plus'
              }
            },
            css_path : ''
          });
        };
      });

      $('.js-media').each(function(e) {
        var $this = $(this);
        $.getJSON(this.title, function(data) {
          $this.html(data['html']);
        });
      });
    </script>
  {/foreach}
{/strip}