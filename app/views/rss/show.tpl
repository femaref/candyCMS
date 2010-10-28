<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
  <channel>
    <title>{$lang_website_title}</title>
    <description>{$_description_}</description>
    <language>{$_language_}</language>
    <link>{$_link_}</link>
    <copyright>{$_copyright_}</copyright>
    {foreach $data as $rss}
      <item>
        <title>{$rss.title}</title>
        <description>
          <![CDATA[
          {$rss.content}
          ]]>
        </description>
        <author>{$rss.name} {$rss.surname}</author>
        <guid>{$WEBSITE_URL}/{$_section_}/{$rss.id}/{$rss.eTitle}</guid>
        <link>{$WEBSITE_URL}/{$_section_}/{$rss.id}/{$rss.eTitle}</link>
      </item>
    {/foreach}
  </channel>
</rss>