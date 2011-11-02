{if $USER_RIGHT >= 3}
  <p class="center">
    <a href='/gallery/{$_request_id_}/createfile'>
      <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='' width="16" height="16" />
      {$lang.gallery.files.title.create}
    </a>
  </p>
{/if}
<section id="gallery">
  {if !$gallery_name}
    <div class='error' title="{$lang.error.missing.entry}">
      <h4>{$lang.error.missing.entry}</h4>
    </div>
  {else}
    <h1>
      {$gallery_name} ({$file_no} {$lang.global.files})
      <a href='/rss/gallery/{$_request_id_}'>
        <img src='%PATH_IMAGES%/spacer.png' class="icon-rss" alt='{$lang.global.rss}' width="16" height="16" />
      </a>
      {if $USER_RIGHT >= 3}
        <a href='/gallery/{$_request_id_}/update'>
          <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang.global.update.update}'
               width="16" height="16" title='{$lang.global.update.update}' />
        </a>
      {/if}
    </h1>
    {if $gallery_content}
      <h3>{$gallery_content}</h3>
    {/if}
    {if !$files}
      <div class='error' title="{$lang.error.missing.files}">
        <h4>{$lang.error.missing.files}</h4>
      </div>
    {else}
      <ul class="js-caption">
        {foreach $files as $f}
          <li>
            <a href='{$f.url_popup}' class="js-fancybox" rel="images" title='{$f.content}'>
              <img src='{$f.url_thumb}'
                   alt='{$f.content}'
                   title='{$f.content}'
                   class="js-image" />
            </a>
            {if $USER_RIGHT >= 3}
              <div>
                <a href="/gallery/{$f.id}/updatefile">
                  <img src="%PATH_IMAGES%/spacer.png" class="icon-update"
                       alt="{$lang.global.update.update}" title="{$lang.global.update.update}" width="16" height="16" />
                </a>
                <a href="#" onclick="candy.system.confirmDestroy('/gallery/{$f.id}/destroyfile')">
                  <img src="%PATH_IMAGES%/spacer.png" class="icon-destroy"
                       alt="{$lang.global.destroy.destroy}" title="{$lang.global.destroy.destroy}" width="16" height="16" />
                </a>
              </div>
            {/if}
          </li>
        {/foreach}
      </ul>
    {/if}
  {/if}
</section>
<script src='%PATH_JS%/core/jquery.fancybox.js' type='text/javascript'></script>
<script src='%PATH_JS%/core/jquery.lazyload.js' type='text/javascript'></script>
<script type="text/javascript">
  $(document).ready(function(){
    $(".js-fancybox").fancybox();
    $(".js-image").lazyload({ threshold : 200, effect : "fadeIn" });
  });
</script>