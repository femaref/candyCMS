<h1>{$lang.global.sitemap}</h1>
<h2>{$lang.global.blog}</h2>
<ol>
  {foreach $blog as $b}
    <li>
      <a href="{$b.url}">{$b.title}</a>
    </li>
  {/foreach}
</ol>
<h2>{$lang.global.content}</h2>
<ol>
  {foreach $content as $c}
    <li>
      <a href="{$c.url}">{$c.title}</a>
    </li>
  {/foreach}
</ol>
<h2>{$lang.global.gallery}</h2>
<ol>
  {foreach $gallery as $g}
    <li>
      <a href="{$g.url}">{$g.title}</a>
    </li>
  {/foreach}
</ol>