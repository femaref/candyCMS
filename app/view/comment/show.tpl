{if $AJAX_REQUEST == false}
  <div id="js-ajax_reload" name="reload">
{/if}
{$_comment_pages_}
{foreach from=$comments item=c name=comments}
  <div class='comment {if $author_id == $c.author_id}from_author{/if}'>
    <h3 class='{if $author_id == $c.author_id}row1{/if}'>
      <a href='#{$c.id}' name='{$c.id}'>#{$c.loop+$comment_number}</a>
      {if $c.user_id > 0}
        <a href='/User/{$c.user_id}'>{$c.name} {$c.surname}</a>
      {elseif $c.author_name !== ''}
        {$c.author_name}
      {else}
        <em style="text-decoration:line-through">{$lang_deleted_user}</em>
      {/if}
      {if $author_id == $c.author_id}&nbsp;({$lang_author}){/if}, {$c.date}
    </h3>
    <div id="c{$c.id}">
      {$c.content}
    </div>
    <div class='{if $author_id == $c.author_id}row1{/if} footer'>
      <a href='#add'
         onclick="quoteMessage('{$c.name} {$c.surname}', 'c{$c.id}')">
        <img src='%PATH_IMAGES%/spacer.gif' class="icon-quote" alt='{$lang_quote}'
             title='{$lang_quote}' />
      </a>
      {if $USER_RIGHT > 3}
        <img src='%PATH_IMAGES%/spacer.gif' class="icon-destroy pointer" alt='{$lang_destroy}'
             onclick="confirmDelete('#{$c.loop+$comment_number}', '/Comment/{$c.id}/destroy/{$c.parent_id}')"
             title='{$lang_destroy}' />
      {/if}
    </div>
  </div>
{/foreach}
{$_comment_pages_}
{if $AJAX_REQUEST == false}
  </div>
{/if}