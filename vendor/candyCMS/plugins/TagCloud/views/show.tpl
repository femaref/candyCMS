{strip}
  <section id='tags'>
    <ul>
      {foreach from=$data item=t}
        <li>
          {if $t.amount == 1}
            <a href='{$t.blogentries[0].url}' title='{$t.blogentries[0].title}' class='js-tooltip'>
              {$t.title}
            </a>
          {else}
            <a href='{$t.url}' title='{$t.title}' class='js-tooltip'>
              {$t.title}
            </a>
          {/if}
        </li>
      {/foreach}
    </ul>
  </section>
{/strip}