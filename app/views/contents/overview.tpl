<section id="content">
  {if $USER_RIGHT >= 3}
    <p class="center">
      <a href='/content/create'>
        <img src='%PATH_IMAGES%/spacer.png' class="icon-create" alt='' width="16" height="16" />
        {$lang_create_entry_headline}
      </a>
    </p>
  {/if}
  <h1>{$lang_headline}</h1>
  <table>
    <tr>
      <th>{$lang_name}</th>
      <th>{$lang_date}</th>
      <th>{$lang_author}</th>
      <th></th>
    </tr>
    {foreach $content as $c}
      <tr class='{cycle values="row1,row2"}'>
        <td>
          <a href='/content/{$c.id}/{$c.encoded_title}'>
            {$c.title}
          </a>
        </td>
        <td>{$c.datetime}</td>
        <td>
          <a href='/user/{$c.author_id}'>
            {$c.name} {$c.surname}
          </a>
        </td>
        {if $USER_RIGHT >= 3}
          <td>
            <a href='/content/{$c.id}/update'>
              <img src='%PATH_IMAGES%/spacer.png' class="icon-update" alt='{$lang_update}'
                title='{$lang_update}' width="16" height="16" />
            </a>
            <img src='%PATH_IMAGES%/spacer.png' class="icon-destroy pointer" alt='{$lang_destroy}'
              title='{$lang_destroy}' width="16" height="16"
              onclick="confirmDelete('/content/{$c.id}/destroy')" />
          </td>
        {/if}
      </tr>
    {/foreach}
  </table>
</section>