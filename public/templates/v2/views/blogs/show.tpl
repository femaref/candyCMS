<section id="blog">
  {if $USER_ROLE >= 3}
    <p class="center">
      <a href='/blog/create'>
        <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='' width="16" height="16" />
        {$lang.global.create.entry}
      </a>
    </p>
  {/if}
  {if !$blog}
    <div class='error' id='js-error' title='{$lang.global.no_entries}'>
      <h4>{$lang.global.no_entries}</h4>
    </div>
  {else}
    {foreach $blog as $b}
      {if !$b.id}
        <div class='error' id='js-error' title='{$lang.error.missing.entry}'>
          <h4>{$lang.global.missing_entry}</h4>
        </div>
      {else}
        <article class="blogs">
          <header>
            <h2>
              {if $b.published == false}
                {$lang.global.not_published}:
              {/if}
              <a href='/blog/{$b.id}/{$b.encoded_title}'>{$b.title}</a>
              {if $USER_ROLE >= 3}
                <a href='/blog/{$b.id}/update'>
                  <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang.global.update}'
                       title='{$lang.global.update}' width="16" height="16" />
                </a>
              {/if}
            </h2>
            <p>
              <time datetime="{$b.date_w3c}">
                {$b.date}
                {if $b.date_modified != ''}
                  - {$lang.global.last_update}: {$b.date_modified}
                {/if}
              </time>
              {if $b.tags[0] !== ''}
                |
                {$lang.global.tags.tags}:
                {foreach from=$b.tags item=t name=tags}
                  <a title='{$lang.global.tags.info}: {$t}' href='/blog/{$t}'>{$t}</a>{if !$t@last}, {/if}
                {/foreach}
              {/if}
            </p>
          </header>
          {if $b.teaser !== ''}
            <p class="summary">{$b.teaser}</p>
          {/if}
          {$b.content}
          <footer>
            {if $_facebook_plugin_ == true}
              <div class="facebook_like">
                <fb:like href="{$b.url_clean}" ref="{$b.id}" width="674" show_faces="false" send="true"></fb:like>
              </div>
            {/if}
          </footer>
        </article>
      {/if}
    {/foreach}
  {/if}
  {* Show comments only if we got a entry *}
  {if isset($b.id)}
    {$_blog_footer_}
  {/if}
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
</section>