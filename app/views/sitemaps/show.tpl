<section id="sitemap">
  <h1>{$lang.global.sitemap}</h1>
  <h3>{$lang.global.blog}</h3>
  <ul>
    {foreach $blog as $b}
      <li>
        <a href="{$b.url}">{$b.title}</a>
      </li>
    {/foreach}
  </ul>
  <h2>{$lang.global.content}</h2>
  <ul>
    {foreach $content as $c}
      <li>
        <a href="{$c.url}">{$c.title}</a>
      </li>
    {/foreach}
  </ul>
  <h2>{$lang.global.gallery}</h2>
  <ul>
    {foreach $gallery as $g}
      <li>
        <a href="{$g.url}">{$g.title}</a>
      </li>
    {/foreach}
  </ul>
</section>