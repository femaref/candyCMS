{if $USER_RIGHT >= 3}
  <p class="center">
    <a href='/gallery/create'>
      <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='' width="16" height="16" />
      {$lang.gallery.albums.title.create}
    </a>
  </p>
{/if}
{if !$albums}
  <div class='error' title='{$lang.error.missing.entries}'>
    <p>{$lang.error.missing.entries}</p>
  </div>
{else}
  <section id='gallery'>
    {foreach $albums as $a}
      <article class='gallery_albums'>
        {* Show gallery albums with uploaded images *}
        {if $a.files_sum > 0}
          <header>
            <h2>
              <a href='{$a.url}'>{$a.title}</a>
              {if $USER_RIGHT >= 3}
                <a href='/gallery/{$a.id}/createfile'>
                  <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt="{$lang.gallery.files.title.create}"
                       title="{$lang.gallery.files.title.create}" width="16" height="16" />
                </a>
                <a href='/gallery/{$a.id}/update'>
                  <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt="{$lang.gallery.albums.title.update}"
                       title="{$lang.gallery.albums.title.update}" width="16" height="16" />
                </a>
              {/if}
            </h2>
            <p>{$a.datetime} - {$a.files_sum} {$lang.global.files}</p>
          </header>
          <a href='/gallery/{$a.id}'>
            {foreach $a.files as $f}
              <img src='{$f.url_32}'
                   alt='{$f.file}' title='{$f.content}'
                   height='32' width='32' />
            {/foreach}
          </a>
        {* Show gallery albums without uploaded images *}
        {elseif $USER_RIGHT >= 3}
          <header>
            <h2>
              <a href='{$a.url}'>{$a.title}</a>
              <a href='/gallery/{$a.id}/createfile'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='{$lang.gallery.files.title.create}'
                     title='{$lang.gallery.files.title.create}' width="16" height="16" />
              </a>
              <a href='/gallery/{$a.id}/update'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang.gallery.albums.title.update}'
                     title='{$lang.gallery.albums.title.update}' width="16" height="16" />
              </a>
            </h2>
            <p>{$a.datetime} - {$a.files_sum} {$lang.global.files}</p>
          </header>
          <p class="error">
            {$lang.error.missing.files}
          </p>
        {/if}
      </article>
    {/foreach}
    {$_pages_}
  </section>
{/if}
<script src='%PATH_JS%/core/jquery.lazyload{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script type="text/javascript">
  $(document).ready(function(){
    $(".image img").lazyload({ effect : "fadeIn" });
  });
</script>