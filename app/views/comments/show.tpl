<section id="comments">
  <header>
    <a name='comments'></a>
    <h3>{$lang_comments}</h3>
  </header>
  <div id="js-commments">
    {foreach $comments as $c}
      <article class='{if $author_id == $c.author_id}from_author{/if}'>
        <header>
          <a href='#{$c.id}' name='{$c.id}' class="count">#{$c.loop+$comment_number}</a>
          <img class="avatar" src="{$c.avatar_64}" width="32" height="32" alt="" />
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
          {if $author_id == $c.author_id}&nbsp;({$lang_author}){/if}
          <br />
          <time datetime="2009-06-29T23:35:20+01:00">{$c.datetime}</time>
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
          <a href='#add'
             onclick="candy.system.quote('{$c.full_name}{$c.author_name}', 'js-comment_{$c.id}')">
            <img src='%PATH_IMAGES%/spacer.png' class="icon-quote" alt='{$lang_quote}'
                 title='{$lang_quote}' />
          </a>
          {if $USER_RIGHT >= 3}
            <img src='%PATH_IMAGES%/spacer.png' class="icon-destroy pointer" alt='{$lang_destroy}'
                 onclick="confirmDelete('/comment/{$c.id}/destroy/{$c.parent_id}')"
                 title='{$lang_destroy}' />
          {/if}
        </footer>
      </article>
    {/foreach}
  </div>
</section>
<div class="navigation">
  {$_comment_pages_}
</div>
<script type="text/javascript">
  $(document).ready(function(){
    $('#js-commments').infinitescroll({
      navSelector  : "div.navigation",
      nextSelector : "div.navigation a:first",
      itemSelector : "#js-commments article",
      loadingImg   : "%PATH_IMAGES%/loading.gif",
      loadingText  : '',
      donetext     : ''
    });
  });
</script>