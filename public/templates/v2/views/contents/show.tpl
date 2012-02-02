<section id="content">
  {if !$content}
    <div class='error' id='js-error' title='{$lang.error.missing.entry}'>
      <h4>{$lang.error.missing.entry}</h4>
    </div>
  {else}
    {foreach $content as $c}
      <article class="contents">
        <header>
          <h1>
            {$c.title}
            {if $USER_ROLE >= 3}
              <a href='/content/{$c.id}/update'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang.global.update}' width="16" height="16" title='{$lang_update}' />
              </a>
            {/if}
          </h1>
          <p>
            <time datetime="{$c.date_w3c}">
              {$lang.global.last_update}: {$c.date}
            </time>
          </p>
        </header>
        {$c.content}
        <footer>
          {if $_facebook_plugin_ == true}
            <div class="facebook_like">
              <fb:like href="{$c.url_clean}" ref="{$c.id}" width="674" show_faces="false" send="true"></fb:like>
            </div>
          {/if}
        </footer>
      </article>
      <script src='%PATH_PUBLIC%/js/core/jquery.fancybox{$_compress_files_suffix_}.js' type='text/javascript'></script>
      <script src='%PATH_PUBLIC%/js/core/jquery.capty{$_compress_files_suffix_}.js' type='text/javascript'></script>
      <script type="text/javascript">
        $(document).ready(function(){
          $(".js-fancybox").fancybox();
          $('.js-image').capty();
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
</section>