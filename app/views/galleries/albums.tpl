{if $USER_ROLE >= 3}
  <p class="center">
    <a href='/gallery/create'>
      <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='' width="16" height="16" />
      {$lang.gallery.albums.title.create}
    </a>
  </p>
{/if}
<div class='page-header'>
  <h1>{$lang.global.gallery}</h1>
</div>
{if !$albums}
  <div class='error' title='{$lang.error.missing.entries}'>
    <h4>{$lang.error.missing.entries}</h4>
  </div>
{else}
  {foreach $albums as $a}
    <article class='gallery_albums'>
      {* Show gallery albums with uploaded images *}
      {if $a.files_sum > 0}
        <header>
          <h2>
            <a href='{$a.url}'>{$a.title}</a>
            {if $USER_ROLE >= 3}
              <a href='/gallery/{$a.id}/createfile'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt="{$lang.gallery.files.title.create}"
                     title="{$lang.gallery.files.title.create}" width="16" height="16" />
              </a>
              <a href='/gallery/{$a.id}/update'>
                <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt="{$lang.global.update.update}"
                     title="{$lang.global.update.update}" width="16" height="16" />
              </a>
            {/if}
          </h2>
          <p>{$a.datetime} - {$a.files_sum} {$lang.global.files}</p>
        </header>
        <ul class="thumbnails">
          {foreach $a.files as $f}
            <li>
              <a href='{$a.url}' class='thumbnail'>
                <img src='/{$f.url_32}'
                     alt='{$f.file}' title='{$f.content}'
                     height='32' width='32' />
              </a>
            </li>
          {/foreach}
        </ul>
      {* Show gallery albums without uploaded images *}
      {elseif $USER_ROLE >= 3}
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
        <h4 class="error">
          {$lang.error.missing.files}
        </h4>
      {/if}
    </article>
  {/foreach}
  {$_pages_}
{/if}
<script src='%PATH_JS%/core/jquery.lazyload{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script type="text/javascript">
  $(document).ready(function(){
    $(".image img").lazyload({ effect : "fadeIn" });
  });
</script>