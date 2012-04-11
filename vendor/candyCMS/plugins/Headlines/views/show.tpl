{strip}
  <section id='headlines'>
    <ul>
      {foreach from=$data item=d}
        <li>
          <a href='{$d.url}' title='{$d.teaser}' class='js-tooltip'>
            {$d.title}
          </a>
        </li>
      {/foreach}
    </ul>
  </section>
{/strip}