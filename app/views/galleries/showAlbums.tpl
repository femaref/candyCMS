{if $USER_RIGHT > 3}
  <p>
    <a href='/Gallery/create'>
      <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='' />
      {$lang_create_entry_headline}
    </a>
  </p>
{/if}
<div class='gallery'>
  {if !$albums}
    <div class='error' id='js-error' title='{$lang_no_entries}' onclick="hideDiv('js-error')">
      <p>{$lang_no_entries}</p>
    </div>
  {else}
    {foreach $albums as $a}
      <div class='gallery_album {cycle values="row1,row2"}'>
        <h2>
          <a href='/Gallery/{$a.id}'>{$a.title}</a>
          {if $USER_RIGHT > 3}
            <a href='/Gallery/{$a.id}/update'>
              <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang_update}'
                    title='{$lang_update}' />
            </a>
          {/if}
        </h2>
        <span class='small'>{$a.datetime} - {$a.files_sum} {$lang_files}</span>
        <p>
          {if $a.files_sum > 0}
            <a href='/Gallery/{$a.id}'>
              {foreach $a.files as $f}
                <img src='{$f.url_32}'
                     alt='{$f.file}' title='{$f.description}' class='image'
                     height='32' width='32' />
              {/foreach}
            </a>
          {else}
            {$lang_no_files_uploaded}
          {/if}
        </p>
      </div>
    {/foreach}
  {/if}
</div>