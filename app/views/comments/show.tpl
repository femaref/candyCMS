<section id="comments">
  <header>
    <a name='comments'></a>
    <h3>{$lang.global.comments}</h3>
  </header>
  <div id="js-commments">
    {foreach $comments as $c}
      <article {if $author_id == $c.author_id}class='from_author'{/if}>
        <header>
          <a href='#{$c.id}' name='{$c.id}' class="count">{$c.loop+$comment_number}</a>
          <img class="avatar" src="{$c.avatar_64}" width="40" height="40" alt="" />
          {if $c.user_id > 0}
            <a href='/user/{$c.user_id}/{$c.encoded_full_name}'>{$c.full_name}</a>
          {elseif $c.author_facebook_id > 0 && isset($c.author_website)}
            <a href='{$c.author_website}'>
              {$c.author_name}
            </a>
          {elseif $c.author_name}
            {$c.author_name}
          {else}
            <em style="text-decoration:line-through">{$lang.global.deleted_user}</em>
          {/if}
          <br />
          <time datetime="{$c.date_w3c}">{$c.datetime}</time>
        </header>
        <div id="js-comment_{$c.id}">
          {$c.content}
        </div>
        <footer>
          {if $USER_RIGHT >= 3 && $c.author_email}
            <a href="mailto:{$c.author_email}">{$c.author_email}</a>
          {/if}
          {if $USER_RIGHT >= 3 && $c.author_ip}
            <span>{$c.author_ip}</span>
          {/if}
          <a href='#create'
             onclick="candy.system.quote('{$c.full_name}{$c.author_name}', 'js-comment_{$c.id}')">
            <img src='%PATH_IMAGES%/spacer.png' class="icon-quote" alt='{$lang.global.quote.quote}' width="16" height="16"
                 title='{$lang.global.quote.quote}' />
          </a>
          {if $USER_RIGHT >= 3}
            <img src='%PATH_IMAGES%/spacer.png' class="icon-destroy pointer" alt='{$lang.comment.title.destroy}'
                 onclick="candy.system.confirmDestroy('/comment/{$c.id}/destroy/{$c.parent_id}')" width="16" height="16"
                 title='{$lang.global.destroy.destroy}' />
          {/if}
        </footer>
      </article>
    {/foreach}
  </div>
</section>
{$_pages_}
<script src='%PATH_JS%/core/jquery.infiniteScroll{$_compress_files_suffix_}.js' type='text/javascript'></script>
<script type="text/javascript">
  $(document).ready(function(){
    $('#js-commments').infinitescroll({
      navSelector  : "section.pages",
      nextSelector : "section.pages a:first",
      itemSelector : "#js-commments article",
      loading : { msgText : '', img: "%PATH_IMAGES%/loading.gif", loadingText  : '', finishedMsg  : '' }
    });
  });
</script>