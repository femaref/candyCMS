{if !$c}
  <div class='error' id='error' title='{$lang_missing_entry}' onclick="hideDiv('error')">
    <p>{$lang_missing_entry}</p>
  </div>
{else}
  <div id='c{$c.id}' class='element'>
    <div class='element_header'>
      <div class='element_header_title'>
        {$c.title}
        {if $UR > 3}
          <a href='/Content/update/{$c.id}'>
            <img src='%PATH_IMAGES%/icons/update.png' alt='{$lang_update}'
                 title='{$lang_update}' />
          </a>
        {/if}
      </div>
      <div class='element_header_date'>
        {$lang_last_update}: {$c.date} {$lang_by}
        <a href='/User/{$c.authorID}'>{$c.name} {$c.surname}</a>
      </div>
    </div>
    <div class='element_body'>
      {$c.content}
    </div>
    <div class='element_footer'>
      {$lang_share}:
      <a href='http://www.facebook.com/share.php?u={$URL}/Content/{$c.id}/{$c.eTitle}&amp;t={$c.eTitle}'
         class='tooltip' title='{$lang_add_bookmark}::http://www.facebook.com'>
        <img src='%PATH_IMAGES%/icons/facebook.png' alt='Facebook' width='16' height='16' />
      </a>
      <a href='http://del.icio.us/post?url={$URL}/Content/{$c.id}/{$c.eTitle}&amp;title={$c.eTitle}'
         class='tooltip' title='{$lang_add_bookmark}::http://del.icio.us'>
        <img src='%PATH_IMAGES%/icons/delicious.png' alt='del.icio.us' width='16' height='16' />
      </a>
      <a href='http://technorati.com/cosmos/search.html?url={$URL}/Content/{$c.id}/{$c.eTitle}'
         class='tooltip' title='{$lang_add_bookmark}::http://technorati.com'>
        <img src='%PATH_IMAGES%/icons/technorati.png' alt='Technorati' width='16' height='16' />
      </a>
      <a href='http://digg.com/submit?phase=2&amp;url={$URL}/Content/{$c.id}/{$c.eTitle}&amp;title={$c.eTitle}'
         class='tooltip' title='{$lang_add_bookmark}::http://digg.com'>
        <img src='%PATH_IMAGES%/icons/digg.png' alt='Digg' width='16' height='16' />
      </a>
      <a href='http://www.mister-wong.de/index.php?action=addurl&amp;bm_url={$URL}/Content/{$c.id}/{$c.eTitle}&amp;bm_description={$c.eTitle}'
         class='tooltip' title='{$lang_add_bookmark}::http://www.mister-wong.de'>
        <img src='%PATH_IMAGES%/icons/mrwong.png' alt='MrWong' width='16' height='16' />
      </a>
    </div>
  </div>
{/if}