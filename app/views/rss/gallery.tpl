<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
  <channel>
    <title>{$_title_}</title>
    <description>{$_description_}</description>
    <language>{$_language_}</language>
    <link>{$_link_}</link>
    <copyright>{$_copyright_}</copyright>
    <pubDate>{$_pubdate_}</pubDate>
    {foreach $data as $d}
      <item>
        <title>{$d.title}</title>
        <pubDate>{$d.date_rss}</pubDate>
        <description>
          <![CDATA[
            {$d.content}
          ]]>
        </description>
        <author>{$d.full_name}</author>
        <guid>{$d.path_popup}</guid>
        <link>{$d.path_popup}</link>
      </item>
    {/foreach}
  </channel>
</rss>