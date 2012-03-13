{strip}
  <div id='comments'>
    <div class='page-header'>
      <a name='comments'></a>
      <h2>{$lang.global.comments}</h2>
    </div>
    <div id='js-commments'>
      {foreach $comments as $c}
        <article{if $c.author_id == $author_id} class='from_author'{/if}>
          <header>
            <a href='#{$c.id}' name='{$c.id}' class='count'>{$c.loop+$comment_number}</a>
            <img class='thumbnail' src='{$c.avatar_64}' width='40' height='40' alt='' />
            {if $c.user_id > 0}
              <a href='/user/{$c.user_id}/{$c.encoded_full_name}' rel='author'>{$c.full_name}</a>
            {elseif $c.author_facebook_id > 0 && isset($c.author_website)}
              <a href='{$c.author_website}' rel='nofollow'>
                {$c.author_name}
              </a>
            {elseif $c.author_name}
              {$c.author_name}
            {else}
              <em style='text-decoration:line-through'>{$lang.global.deleted_user}</em>
            {/if}
            <br />
            <time datetime='{$c.datetime_w3c}'>{$c.datetime}</time>
          </header>
          <div id='js-comment_{$c.id}'>
            {$c.content}
          </div>
          <footer>
            {if $USER_ROLE >= 3 && $c.author_email}
              <a href='mailto:{$c.author_email}'>{$c.author_email}</a>
              &nbsp;
            {/if}
            {if $USER_ROLE >= 3 && $c.author_ip}
              <span>{$c.author_ip}</span>
              &nbsp;
            {/if}
            <a href='#create' rel='nofollow'
              onclick="quote('{$c.full_name}{$c.author_name}', 'js-comment_{$c.id}')">
              <img src='{$_PATH.images}/candy.global/spacer.png'
                   class='icon-quote js-tooltip'
                   alt='{$lang.global.quote.quote}'
                   width='16' height='16'
                   title='{$lang.global.quote.quote}' />
            </a>
            {if $USER_ROLE >= 3}
              &nbsp;
              <a href="#" onclick="confirmDestroy('/comment/{$c.id}/destroy/{$c.parent_id}')">
                <img src='{$_PATH.images}/candy.global/spacer.png'
                    class='icon-destroy js-tooltip'
                    alt='{$lang.global.destroy.destroy}'
                    title='{$lang.global.destroy.destroy}'
                    width='16' height='16' />
              </a>
            {/if}
          </footer>
        </article>
      {/foreach}
    </div>
  </div>
  {$_pages_}
  <script src='{$_PATH.js}/core/jquery.infiniteScroll{$_compress_files_suffix_}.js' type='text/javascript'></script>
  <script type="text/javascript">
    $(document).ready(function(){
      $('#js-commments').infinitescroll({
        navSelector  : "div.pagination",
        nextSelector : "div.pagination a:first",
        itemSelector : "#js-commments article",
        loading : { msgText : '', img: "{$_PATH.images}/candy.global/loading.gif", loadingText  : '', finishedMsg  : '' }
      });
    });
  </script>
{/strip}