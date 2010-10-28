<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
  <channel>
    <title>{$lang_website_title}</title>
    <description>{$WEBSITE_SLOGAN}</description>
    <language>{$_language_}</language>
    <link>{$WEBSITE_URL}</link>
    <copyright>{$WEBSITE_NAME}</copyright>
    <pubDate>{$_pubdate_}</pubDate>
    {foreach $data as $rss}
      <item>
        <title>{$rss.title}</title>
        <pubDate>{$rss.date_rss}</pubDate>
        <description>
          <![CDATA[
            {if $rss.teaser}
              {$rss.teaser}
            {/if}
            {$rss.content}
          ]]>
        </description>
        <author>{$rss.name} {$rss.surname}</author>
        <comments>{$WEBSITE_URL}/{$_section_}/{$rss.id}/{$rss.eTitle}</comments>
        <guid>{$WEBSITE_URL}/{$_section_}/{$rss.id}/{$rss.eTitle}</guid>
        <link>{$WEBSITE_URL}/{$_section_}/{$rss.id}/{$rss.eTitle}</link>
      </item>
    {/foreach}
  </channel>
</rss>