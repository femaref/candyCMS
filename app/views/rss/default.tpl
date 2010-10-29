<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
  <channel>
    <title>{$lang_website_title}</title>
    <description>{$WEBSITE_SLOGAN}</description>
    <language>{$_language_}</language>
    <link>{$WEBSITE_URL}</link>
    <copyright>{$WEBSITE_NAME}</copyright>
    <pubDate>{$_pubdate_}</pubDate>
    {foreach $data as $d}
      <item>
        <title>{$d.title}</title>
        <pubDate>{$d.date_rss}</pubDate>
        <description>
          <![CDATA[
            {if $d.teaser}
              {$d.teaser}
            {/if}
            {$d.content}
          ]]>
        </description>
        <dc:creator>{$d.full_name}</dc:creator>
        <comments>{$d.url}</comments>
        <guid isPermaLink="true">{$d.url}</guid>
        <link>{$d.url}</link>
      </item>
    {/foreach}
  </channel>
</rss>