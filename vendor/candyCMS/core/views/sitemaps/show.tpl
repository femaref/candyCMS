{strip}
  <div class='page-header'>
    <h1>{$lang.global.sitemap}</h1>
  </div>
  <div class='tabbable'>
    <ul class='nav nav-tabs'>
      <li class='active'>
        <a href='#sitemap-blogs' data-toggle='tab'>{$lang.global.blogs}</a>
      </li>
      <li>
        <a href='#sitemap-contents' data-toggle='tab'>{$lang.global.contents}</a>
      </li>
      <li>
        <a href='#sitemap-galleries' data-toggle='tab'>{$lang.global.galleries}</a>
      </li>
    </ul>
    <div class='tab-content'>
      <div class='tab-pane active' id='sitemap-blogs'>
        {if !$blogs}
          <div class='alert alert-warning'>
            <h4>{$lang.error.missing.entries}</h4>
          </div>
        {else}
          <ol>
            {foreach $blogs as $b}
              <li>
                <a href='{$b.url}'>{$b.title}</a>
              </li>
            {/foreach}
          </ol>
        {/if}
      </div>
      <div class='tab-pane' id='sitemap-contents'>
        {if !$contents}
          <div class='alert alert-warning'>
            <h4>{$lang.error.missing.entries}</h4>
          </div>
        {else}
          <ol>
            {foreach $contents as $c}
              <li>
                <a href='{$c.url}'>{$c.title}</a>
              </li>
            {/foreach}
          </ol>
        {/if}
      </div>
      <div class='tab-pane' id='sitemap-galleries'>
        {if !$galleries}
          <div class='alert alert-warning'>
            <h4>{$lang.error.missing.entries}</h4>
          </div>
        {else}
          <ol>
            {foreach $galleries as $g}
              <li>
                <a href='{$g.url}'>{$g.title}</a>
              </li>
            {/foreach}
          </ol>
        {/if}
      </div>
    </div>
  </div>
  <script type='text/javascript' src='{$_PATH.js}/core/jquery.bootstrap.tabs{$_SYSTEM.compress_files_suffix}.js'></script>
{/strip}