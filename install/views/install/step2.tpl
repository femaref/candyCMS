<form action='?action=install&step=3' method='post'>
  <h2>
    Set following dirs to <em>CHMOD 777 (recursive)</em>.
  </h2>
  <p>
    You might have to use a FTP programm (like <a href='http://cyberduck.ch/'>Cyberduck</a>)
    for that. If the folders were not created by the system, you have to create
    them manually.
  </p>
  <ul>
    {foreach $_folder_checks_ as $folder=>$check}
      <li style='color:{if $check}green{else}red{/if}'>
        {if !$check}<strong>{/if}
          {$folder}
        {if !$check}</strong>{/if}
      </li>
    {/foreach}
  </ul>
  <div class='form-actions right'>
    <input type='submit' class='btn' value='Step 3: Create database &rarr;' />
  </div>
</form>
