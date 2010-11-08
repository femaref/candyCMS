{if !$c}
  <div class='error' id='js-error' title='{$lang_missing_entry}' onclick="hideDiv('js-error')">
    <p>{$lang_missing_entry}</p>
  </div>
{else}
  <div id='c{$c.id}' class='element'>
    <div class='date' title="{$c.datetime}">
      {$c.date}
    </div>
    <div class='header'>
      <h2>
        {$c.title}
        {if $USER_RIGHT > 3}
          <a href='/Content/{$c.id}/update'>
            <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang_update}'
                 title='{$lang_update}' />
          </a>
        {/if}
      </h2>
    </div>
    {$c.content}
    <div class='footer'>
      <div class="share">
        {$lang_share}:
      </div>
      <iframe src="http://www.facebook.com/plugins/like.php?href={$c.encoded_url}&amp;layout=button_count&amp;show_faces=false&amp;width=125&amp;action=like&amp;colorscheme=light&amp;height=21"
              scrolling="no"
              frameborder="0"
              style="border:none;overflow:hidden;width:125px;height:21px"
              allowTransparency="true">
      </iframe>
      <iframe allowtransparency="true"
              frameborder="0"
              scrolling="no"
              src="http://platform.twitter.com/widgets/tweet_button.html?url={$c.url}&amp;text={$c.title}"
              style="width:130px;height:21px">
      </iframe>
    </div>
  </div>
{/if}