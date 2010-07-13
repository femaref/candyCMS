{if $UR > 3}
  <p>
    <a href='/Gallery/create'>
      <img src='%PATH_IMAGES%/icons/create.png' alt='' />
      {$lang_create_entry_headline}
    </a>
  </p>
{/if}
<div class='gallery'>
  {if !$albums}
    <div class='error' id='error' title='{$lang_no_entries}' onclick="hideDiv('error')">
      <p>{$lang_no_entries}</p>
    </div>
  {else}
    {foreach from=$albums item=a}
      <div class='gallery_album' style='background:{cycle values="transparent,#eee"}'>
        <h2>
          <a href='/Gallery/{$a.id}'>{$a.title}</a>
          {if $UR > 3}
            <a href='/Gallery/update/{$a.id}'>
              <img src='%PATH_IMAGES%/icons/update.png' alt='{$lang_update}'
                    title='{$lang_update}' />
            </a>
          {/if}
        </h2>
        <span class='small'>{$a.date} - {$a.files_sum} {$lang_files}</span>
        {if $a.description !== ''}
          <span class='small i'>{$a.description}</span>
        {/if}
        <p>
          {if $a.files_sum > 0}
            <a href='/Gallery/{$a.id}'>
              {foreach from=$a.files item=f}
                <img src='{$f.full_path}/32/{$f.file}'
                     alt='{$f.file}' title='{$f.description}' class='image'
                     height='32' width='32' />
              {/foreach}
            </a>
          {else}
            {$lang_no_files_yet}
          {/if}
        </p>
      </div>
    {/foreach}
  {/if}
</div>