<div id="js-ajax_reload">
  {$commentPages}
  {foreach from=$comments item=c name=comments}
    <div class='comment {if $authorID == $c.authorID}from_author{/if}'>
      <h3 class='{if $authorID == $c.authorID}row1{/if}'>
        <a name='{$c.id}'></a>
        <a href='/Blog/{$c.parentID}#{$c.id}'>#{$c.loop+$commentNumber}</a>
        &nbsp;
        {if $c.userID > 0}
          <a href='/User/{$c.userID}'>{$c.name} {$c.surname}</a>
        {else}
          <em>{$lang_deleted_user}</em>
        {/if}
        {if $authorID == $c.authorID}({$lang_author}){/if}, {$c.date}
      </h3>
      {$c.content}
      <div class='{if $authorID == $c.authorID}row1{/if} footer'>
        {if $uid > 0}
          <a href='#add'
             onclick="quoteMessage('{$c.name} {$c.surname}', 'c{$c.id}')">
            <img src='%PATH_IMAGES%/icons/quote.png' alt='{$lang_quote}'
                 title='{$lang_quote}' />
          </a>
        {/if}
        {if $UR > 3}
          <img src='%PATH_IMAGES%/icons/destroy.png' alt='{$lang_destroy}'
               onclick="confirmDelete('#{$c.loop+$commentNumber}', '/DestroyComment/{$c.id}/{$c.parentID}')"
               class='pointer' title='{$lang_destroy}' />
        {/if}
      </div>
    </div>
  {/foreach}
  {$commentPages}
</div>