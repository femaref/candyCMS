<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>{$_title_}</title>
    <description>{$_content_}</description>
    <language>{$WEBSITE_LANGUAGE}</language>
    <link>{$_link_}</link>
    <copyright>{$_copyright_}</copyright>
    <pubDate>{$smarty.now|date_format:'%a, %d %b %Y %H:%M:%S %z'}</pubDate>
    <atom:link href="{$CURRENT_URL}" rel="self" type="application/rss+xml" />
    {foreach $data as $d}
    <item>
      <title>{$d.file}</title>
      <pubDate>{$d.datetime_rss}</pubDate>
      <guid isPermaLink="false">{$d.url_popup}</guid>
      <link>{$d.url_popup}</link>
      <description>
        <![CDATA[
        <img src="{$WEBSITE_URL}/{$d.url_thumb}"
            width="{$d.thumb_width}"
            height="{$d.thumb_height}"
            alt="{$d.file}" />
        {if $d.content}
          <p>{$d.content}</p>
        {/if}
        ]]>
      </description>
      <media:title>{$d.file}</media:title>
      <media:description><![CDATA[{$d.content}]]></media:description>
      <media:thumbnail
        url="{$WEBSITE_URL}/{$d.url_thumb}"
        width="{$d.thumb_width}"
        height="{$d.thumb_height}" />
      <media:content
        url="{$WEBSITE_URL}/{$d.url_popup}"
        height="{$d.popup_height}"
        width="{$d.popup_width}"
        fileSize="{$d.popup_size}"
        type="{$d.popup_mime}" />
    </item>
    {/foreach}
  </channel>
</rss>