{strip}
  <div class='page-header'>
    <h1>{$lang.global.sitemap}</h1>
  </div>
  <div class='tabbable'>
    <ul class='nav nav-tabs'>
      <li class='active'>
        <a href='#sitemap-blog' data-toggle='tab'>{$lang.global.blog}</a>
      </li>
      <li>
        <a href='#sitemap-content' data-toggle='tab'>{$lang.global.content}</a>
      </li>
      <li>
        <a href='#sitemap-gallery' data-toggle='tab'>{$lang.global.gallery}</a>
      </li>
    </ul>
    <div class='tab-content'>
      <div class='tab-pane active' id='sitemap-blog'>
        {if !$blog}
          <div class='alert alert-warning'>
            <h4>{$lang.error.missing.entries}</h4>
          </div>
        {else}
          <ol>
            {foreach $blog as $b}
              <li>
                <a href='{$b.url}'>{$b.title}</a>
              </li>
            {/foreach}
          </ol>
        {/if}
      </div>
      <div class='tab-pane' id='sitemap-content'>
        {if !$content}
          <div class='alert alert-warning'>
            <h4>{$lang.error.missing.entries}</h4>
          </div>
        {else}
          <ol>
            {foreach $content as $c}
              <li>
                <a href='{$c.url}'>{$c.title}</a>
              </li>
            {/foreach}
          </ol>
        {/if}
      </div>
      <div class='tab-pane' id='sitemap-gallery'>
        {if !$gallery}
          <div class='alert alert-warning'>
            <h4>{$lang.error.missing.entries}</h4>
          </div>
        {else}
          <ol>
            {foreach $gallery as $g}
              <li>
                <a href='{$g.url}'>{$g.title}</a>
              </li>
            {/foreach}
          </ol>
        {/if}
      </div>
    </div>
  </div>
  <script type='text/javascript' src='{$_PATH.js}/core/jquery.bootstrap.tabs{$_compress_files_suffix_}.js'></script>
{/strip}