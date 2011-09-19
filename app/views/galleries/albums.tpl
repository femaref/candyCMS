{if $USER_RIGHT >= 3}
  <p class="center">
    <a href='/gallery/create'>
      <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='' width="16" height="16" />
      {$lang_create_album_headline}
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
        {if $a.files_sum > 0}
          <header>
            <h2>
              <a href='{$a.url}'>{$a.title}</a>
              {if $USER_RIGHT >= 3}
                <a href='/gallery/{$a.id}/createfile'>
                  <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='{$lang_create_file_headline}'
                       title='{$lang_create_file_headline}' width="16" height="16" />
                </a>
                <a href='/gallery/{$a.id}/update'>
                  <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang_update}'
                       title='{$lang_update}' width="16" height="16" />
                </a>
              {/if}
            </h2>
            <p>{$a.datetime} - {$a.files_sum} {$lang.global.files}</p>
          </header>
          <summary>
            <a href='/gallery/{$a.id}'>
              {foreach $a.files as $f}
                <img src='{$f.url_32}'
                     alt='{$f.file}' title='{$f.content}'
                     height='32' width='32' />
              {/foreach}
            </a>
          </summary>
        {elseif $USER_RIGHT >= 3}
          <header>
            <h2>
              <a href='{$a.url}'>{$a.title}</a>
              <a href='/gallery/{$a.id}/createfile'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='{$lang_create_file_headline}'
                     title='{$lang_create_file_headline}' width="16" height="16" />
              </a>
              <a href='/gallery/{$a.id}/update'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang_update}'
                     title='{$lang_update}' width="16" height="16" />
              </a>
            </h2>
            <p>{$a.datetime} - {$a.files_sum} {$lang_files}</p>
          </header>
          <p class="error">
            {$lang_no_files_uploaded}
          </p>
        {/if}
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