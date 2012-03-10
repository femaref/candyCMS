{strip}
  {foreach $user as $u}
    <div class='page-header'>
      <h1>
        {$u.full_name}
        {if $USER_ROLE == 4 || $u.id == $USER_ID}
          <a href='/user/{$_REQUEST.id}/update'>
            <img src='%PATH_IMAGES%/candy.global/spacer.png'
                class='icon-update js-tooltip'
                alt='{$lang.global.update.update}'
                title='{$lang.global.update.update}'
                width='16' height='16' />
          </a>
        {/if}
      </h1>
    </div>
    <table class="table unstyled">
      <tr>
        <td>
          {$lang.user.label.registered_since}
        </td>
        <td>
          {$u.date}
        </td>
        <td rowspan='4'>

          {* List as a fix to fit width *}
          <ul class='thumbnails'>
            <li>
              <a href='{$u.avatar_popup}'
                 class='thumbnail js-fancybox'
                 title='{$u.full_name}'>
                <img alt='{$u.full_name}'
                     src='{$u.avatar_100}'
                     width='100' />
              </a>
            </li>
          </ul>
        </td>
      </tr>
      <tr>
        <td>
          {$lang.user.label.last_login}
        </td>
        <td>
          {$u.last_login}
        </td>
      </tr>
      <tr>
        <td>
          {$lang.user.label.content.show|replace:'%u':$u.name}
        </td>
        <td>
          {$u.content}
        </td>
      </tr>
      <tr>
        <td>
          {$lang.global.contact}
        </td>
        <td>
          {* Absolute URL due to fancybox bug *}
          <a href='{$WEBSITE_URL}/mail/{$_REQUEST.id}'>
            {$lang.user.contact_via_email|replace:'%u':$u.name}
          </a>
        </td>
      </tr>
    </table>
  {/foreach}
{/strip}