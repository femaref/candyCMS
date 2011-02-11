<table>
  <tr>
    <th colspan='7'>{$lang_headline}</th>
  </tr>
  {foreach $logs as $l}
    <tr class='{cycle values="row1,row2"}'>
      <td style='width:25%' class="left">
        <a href='/user/{$l.uid}'>{$l.name} {$l.surname}</a>
      </td>
      <td style='width:10%' class="left">
        {$l.section_name}
      </td>
      <td style='width:10%' class="left">
        {$l.action_name}
      </td>
      <td style='width:5%'>
        {$l.action_id}
      </td>
      <td style='width:20%'>
        {$l.time_start}
      </td>
      <td style='width:20%'>
        {$l.time_end}
      </td>
      <td style='width:10%'>
        {if $USER_RIGHT == 4}
          <img src='%PATH_IMAGES%/spacer.png' class="icon-destroy" alt='{$lang_destroy}'
               title='{$lang_destroy}' class="pointer"
               onclick="confirmDelete('/log/{$l.id}/destroy')" />
        {/if}
      </td>
    </tr>
  {/foreach}
</table>