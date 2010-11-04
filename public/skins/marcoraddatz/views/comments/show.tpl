{if $AJAX_REQUEST == false}
  <div id="js-ajax_reload" name="reload">
{else}
  {if $_plugin_lazyload_}
    {$_plugin_lazyload_}
  {/if}
{/if}
{$_comment_pages_}
{foreach $comments as $c}
  <div class="avatar">
    <div class="image">
      <img src="{$c.avatar_64}" class="{if $author_id == $c.author_id}from_author{else}not_from_author{/if}" alt="{$c.full_name}" />
    </div>
  </div>
  <div class='comment {if $author_id == $c.author_id}from_author{else}not_from_author{/if}'>
    <h3 class='{if $author_id == $c.author_id}row1{/if}'>
      <a href='#{$c.id}' name='{$c.id}'>#{$c.loop+$comment_number}</a>
      {if $c.user_id > 0}
        <a href='/User/{$c.user_id}/{$c.full_name_seo}'>{$c.full_name}</a>
      {elseif $c.author_facebook_id > 0}
        <img src='%PATH_IMAGES%/spacer.png' class="icon-facebook" alt='Facebook' width='16' height='16' />
        <a href='http://www.facebook.com/?uid={$c.author_facebook_id}'>
          {$c.author_name}
        </a>
      {elseif $c.author_name}
        {$c.author_name}
      {else}
        <em style="text-decoration:line-through">{$lang_deleted_user}</em>
      {/if}
      {if $author_id == $c.author_id}&nbsp;({$lang_author}){/if}, {$c.datetime}
    </h3>
    <div id="c{$c.id}">
      {$c.content}
    </div>
    <div class='footer'>
      {if $USER_RIGHT > 3 && $c.author_email}
        <a href="mailto:{$c.author_email}">{$c.author_email}</a>
      {/if}
      {if $USER_RIGHT > 3 && $c.author_ip}
        <span>{$c.author_ip}</span>
      {/if}
      <a href='#add'
         onclick="quoteMessage('{$c.full_name}', 'c{$c.id}')">
        <img src='%PATH_IMAGES%/spacer.png' class="icon-quote" alt='{$lang_quote}'
             title='{$lang_quote}' />
      </a>
      {if $USER_RIGHT > 3}
        <img src='%PATH_IMAGES%/spacer.png' class="icon-destroy pointer" alt='{$lang_destroy}'
             onclick="confirmDelete('/Comment/{$c.id}/destroy/{$c.parent_id}')"
             title='{$lang_destroy}' />
      {/if}
    </div>
  </div>
{/foreach}
<br style="clear:both" />
{$_comment_pages_}
{if $AJAX_REQUEST == false}
  </div>
{/if}
