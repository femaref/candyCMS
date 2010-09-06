<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
  <channel>
    <title>{$title}</title>
    <description>{$description}</description>
    <language>de-de</language>
    <link>{$link}</link>
    <copyright>{$copyright}</copyright>
    {foreach from=$data item=rss}
      <item>
      {if $action == 'blog'}
        <title>{$rss.title}</title>
        <description>
          <![CDATA[
            {$rss.content}
          ]]>
        </description>
        <author>{$rss.name} {$rss.surname}</author>
        <guid>{$link}/Blog/{$rss.id}/{$rss.eTitle}</guid>
        <link>{$link}/Blog/{$rss.id}/{$rss.eTitle}</link>
      {/if}
      </item>
    {/foreach}
  </channel>
</rss>