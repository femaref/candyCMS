<div class="comments">
  {foreach $comments as $c}
    <div class='comment {if $author_id == $c.author_id}from_author{/if}'>
      <h3>
        <a href='#{$c.id}' name='{$c.id}'>#{$c.loop+$comment_number}</a>
        {if $c.user_id > 0}
          <a href='/user/{$c.user_id}/{$c.encoded_full_name}'>{$c.full_name}</a>
        {elseif $c.author_facebook_id > 0}
          <a href='{$c.author_website}'>
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
        {if $USER_RIGHT >= 3 && $c.author_email}
          <a href="mailto:{$c.author_email}">{$c.author_email}</a>
        {/if}
        {if $USER_RIGHT >= 3 && $c.author_ip}
          <span>{$c.author_ip}</span>
        {/if}
        <a href='#add'
           onclick="candy.system.quote('{$c.full_name}{$c.author_name}', 'c{$c.id}')">
          <img src='%PATH_IMAGES%/spacer.png' class="icon-quote" alt='{$lang_quote}'
               title='{$lang_quote}' />
        </a>
        {if $USER_RIGHT >= 3}
          <img src='%PATH_IMAGES%/spacer.png' class="icon-destroy pointer" alt='{$lang_destroy}'
               onclick="confirmDelete('/Comment/{$c.id}/destroy/{$c.parent_id}')"
               title='{$lang_destroy}' />
        {/if}
      </div>
    </div>
  {/foreach}
</div>
<div class="navigation">
  {$_comment_pages_}
</div>
<script type="text/javascript">
  $(document).ready(function(){
    $('.comments').infinitescroll({
      navSelector  : "div.navigation",
      nextSelector : "div.navigation a:first",
      itemSelector : ".comments",
      loadingImg   : "%PATH_IMAGES%/loading.gif",
      loadingText  : '',
      donetext     : ''
    });
  });
</script>