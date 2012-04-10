<form action='?action=install&step=4' method='post' class='form-horizontal'>
  {if $_has_errors_}
    <div class='alert alert-danger'>
      <h4 class='alert-heading'>
        Ooops!
      </h4>
      <p>
        Some errors occured during database creation. Please restart installation.
      </p>
    </div>
  {else}
    <div class='alert alert-success'>
      <h4 class='alert-heading'>
        Database creation was successfull.
      </h4>
    </div>
    <div class='form-actions right'>
      <input type='submit' class='btn' value='Step 4: Create admin user &rarr;' />
    </div>
  {/if}
</form>