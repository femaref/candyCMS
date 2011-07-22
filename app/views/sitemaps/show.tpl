<section id="sitemap">
  <h1>{$lang_sitemap}</h1>
  <h2>{$lang_blog}</h2>
  <ul>
    {foreach $blog as $b}
      <li>
        <a href="{$b.url}">{$b.title}</a>
      </li>
    {/foreach}
  </ul>
  <h2>{$lang_content}</h2>
  <ul>
    {foreach $content as $c}
      <li>
        <a href="{$c.url}">{$c.title}</a>
      </li>
    {/foreach}
  </ul>
  <h2>{$lang_gallery}</h2>
  <ul>
    {foreach $gallery as $g}
      <li>
        <a href="{$g.url}">{$g.title}</a>
      </li>
    {/foreach}
  </ul>
  <h2>{$lang_user}</h2>
  <ul>
    {foreach $user as $u}
      <li>
        <a href="{$u.url}">{$u.full_name}</a>
      </li>
    {/foreach}
  </ul>
</section>