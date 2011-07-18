{if $USER_RIGHT >= 3}
  <p class="center">
    <a href='/gallery/create'>
      <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='' />
      {$lang_create_entry_headline}
    </a>
  </p>
{/if}
{if !$albums}
  <div class='error' id='js-error' title='{$lang_no_entries}'>
    <p>{$lang_no_entries}</p>
  </div>
{else}
  <section id='gallery'>
    {foreach $albums as $a}
      <article class='gallery_albums'>
        <header>
          <h2>
            <a href='{$a.url}'>{$a.title}</a>
            {if $USER_RIGHT >= 3}
              <a href='/gallery/{$a.id}/update'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang_update}'
                     title='{$lang_update}' />
              </a>
            {/if}
          </h2>
          <p>{$a.datetime} - {$a.files_sum} {$lang_files}</p>
        </header>
        <details open='open'>
          {if $a.files_sum > 0}
            <a href='/gallery/{$a.id}'>
              {foreach $a.files as $f}
                <img src='{$f.url_32}'
                     alt='{$f.file}' title='{$f.description}'
                     height='32' width='32' />
              {/foreach}
            </a>
          {else}
            {$lang_no_files_uploaded}
          {/if}
        </details>
      </article>
    {/foreach}
    {$_pages_}
  </section>
{/if}
<script src='%PATH_PUBLIC%/js/core/jquery.lazyload{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script type="text/javascript">
  $(document).ready(function(){
    $(".image img").lazyload({ effect : "fadeIn" });
  });
</script>