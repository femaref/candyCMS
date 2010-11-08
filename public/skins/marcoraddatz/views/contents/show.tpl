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
      <iframe src="http://www.facebook.com/plugins/like.php?href={$b.url}&
              layout=button_count&
              show_faces=false&
              ref={$b.eTitle}"
              scrolling="no"
              frameborder="0"
              style="border:none;overflow:hidden;width:125px;height:21px"
              allowTransparency="true">
      </iframe>
    </div>
  </div>
{/if}