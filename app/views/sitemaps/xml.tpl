<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>{$_website_landing_page_}</loc>
    <priority>1.0</priority>
    <changefreq>hourly</changefreq>
  </url>
  {foreach $blog as $b}
    <url>
      <loc>{$b.url}</loc>
      <priority>0.75</priority>
      <changefreq>weekly</changefreq>
    </url>
  {/foreach}
  {foreach $content as $c}
    <url>
      <loc>{$c.url}</loc>
      <priority>0.25</priority>
      <changefreq>monthly</changefreq>
    </url>
  {/foreach}
  {foreach $gallery as $g}
    <url>
      <loc>{$g.url}</loc>
      <priority>0.5</priority>
      <changefreq>weekly</changefreq>
    </url>
  {/foreach}
</urlset>