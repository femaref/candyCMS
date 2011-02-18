{if $USER_RIGHT >= 3}
  <p>
    <a href='/content/create'>
      <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='' />
      {$lang_create_entry_headline}
    </a>
  </p>
{/if}
<table>
  <tr>
    <th colspan='4'>{$lang_headline}</th>
  </tr>
  {foreach $content as $c}
    <tr class='{cycle values="row1,row2"}'>
      <td style='width:45%' class="left">
        <a href='/Content/{$c.id}/{$c.encoded_title}'>
          {$c.title}
        </a>
      </td>
      <td style='width:25%'>{$c.datetime}</td>
      <td style='width:20%'>
        <a href='/user/{$c.author_id}'>
          {$c.name} {$c.surname}
        </a>
      </td>
      <td style='width:10%'>
        {if $USER_RIGHT >= 3}
          <a href='/content/{$c.id}/update'>
            <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang_update}'
              title='{$lang_update}' />
          </a>
          <img src='%PATH_IMAGES%/spacer.png' class="icon-destroy pointer" alt='{$lang_destroy}'
            title='{$lang_destroy}'
            onclick="confirmDelete('/content/{$c.id}/destroy')" />
        {/if}
      </td>
    </tr>
  {/foreach}
</table>