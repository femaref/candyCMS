<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>{$_title_}</title>
    <description>{$lang.website.description}</description>
    <language>{$WEBSITE_LANGUAGE}</language>
    <link>{$WEBSITE_URL}</link>
    <copyright>{$WEBSITE_NAME}</copyright>
    <pubDate>{$_pubdate_}</pubDate>
    <atom:link href="{$CURRENT_URL}" rel="self" type="application/rss+xml" />
    {foreach $data as $d}
      <item>
        <title>{$d.title}</title>
        <pubDate>{$d.datetime_rss}</pubDate>
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