<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/">
  <channel>
    <title>{$_title_}</title>
    <description>{$_description_}</description>
    <language>{$_language_}</language>
    <link>{$_link_}</link>
    <copyright>{$_copyright_}</copyright>
    <pubDate>{$_pubdate_}</pubDate>
    {foreach $data as $d}
      <item>
        <title>{$d.file}</title>
        <pubDate>{$d.date_rss}</pubDate>
        <guid isPermaLink="false">{$d.url_popup}</guid>
        <link>{$d.url_popup}</link>
        <description>
          <![CDATA[
            <img src="{$d.url_thumb}"
                 width="{$d.thumb_width}"
                 height="{$d.thumb_height}"
                 alt="{$d.file}" />
          ]]>
        </description>
        <media:title>{$d.file}</media:title>
        <media:description><![CDATA[{$d.description}]]></media:description>
        <media:thumbnail
          url="{$d.url_thumb}"
          width="{$d.thumb_width}"
          height="{$d.thumb_height}" />
        <media:content
          url="{$d.url_popup}"
          height="{$d.popup_height}"
          width="{$d.popup_width}"
          fileSize="{$d.popup_size}"
          type="{$d.popup_mime}" />
      </item>
    {/foreach}
  </channel>
</rss>