{strip}
  <section id='tags'>
    <ul>
      {foreach from=$data item=t}
        <li>
          {if $t.amount == 1}
            <a href='{$t.blogentries[0].url}' title='{$t.blogentries[0].title}' class='js-tooltip'>
          {else}
            <a href='{$t.url}'>
          {/if}
              {$t.title} {if $t.amount >= 1}({$t.amount}){/if}
            </a>
        </li>
      {/foreach}
    </ul>
  </section>
{/strip}