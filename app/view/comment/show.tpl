<div id="js-ajax_reload">
  {foreach from=$comments item=c name=comments}
    <div class='comment {if $authorID == $c.authorID}from_author{/if}'>
      <div class='{if $authorID == $c.authorID}row1{/if} comment_header'>
        <a name='{$c.id}'></a>
        <a href='/Blog/{$c.parentID}#{$c.id}'>#{$c.loop+$commentNumber}</a>
        &nbsp;
        {if $c.userID > 0}
          <a href='/User/{$c.userID}'>{$c.name} {$c.surname}</a>
        {else}
          <span style='font-style:italic'>{$lang_deleted_user}</span>
        {/if}
        {if $authorID == $c.authorID}({$lang_author}){/if}, {$c.date}
      </div>
      <div id='c{$c.id}' class='{if $authorID == $c.authorID}row1{/if} comment_body'>
        {$c.content}
      </div>
      <div class='{if $authorID == $c.authorID}row1{/if} comment_footer'>
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