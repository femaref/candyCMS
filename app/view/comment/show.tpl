{if $AJAX == false}
  <div id="js-ajax_reload" name="reload">
{/if}
{$commentPages}
{foreach from=$comments item=c name=comments}
  <div class='comment {if $authorID == $c.authorID}from_author{/if}'>
    <h3 class='{if $authorID == $c.authorID}row1{/if}'>
      <a href='/Blog/{$c.parentID}#{$c.id}' name='{$c.id}'>#{$c.loop+$commentNumber}</a>
      {if $c.userID > 0}
        <a href='/User/{$c.userID}'>{$c.name} {$c.surname}</a>
      {elseif $c.author_name !== ''}
        {$c.author_name}
      {else}
        <em style="text-decoration:line-through">{$lang_deleted_user}</em>
      {/if}
      {if $authorID == $c.authorID}&nbsp;({$lang_author}){/if}, {$c.date}
    </h3>
    <div id="c{$c.id}">
      {$c.content}
    </div>
    <div class='{if $authorID == $c.authorID}row1{/if} footer'>
      <a href='#add'
         onclick="quoteMessage('{$c.name} {$c.surname}', 'c{$c.id}')">
        <img src='%PATH_IMAGES%/spacer.gif' class="icon-quote" alt='{$lang_quote}'
             title='{$lang_quote}' />
      </a>
      {if $UR > 3}
        <img src='%PATH_IMAGES%/spacer.gif' class="icon-destroy" alt='{$lang_destroy}'
             onclick="confirmDelete('#{$c.loop+$commentNumber}', '/DestroyComment/{$c.id}/{$c.parentID}')"
             class='pointer' title='{$lang_destroy}' />
      {/if}
    </div>
  </div>
{/foreach}
{$commentPages}
{if $AJAX == false}
  </div>
{/if}