<form action='?action=install&step=4' method='post' class='form-horizontal'>
  {if $_has_errors_}
    <div class="alert alert-danger">
      Some Errors occured during Database creation.
    </div>
  {else}
    <div class="alert">
      Database Creation was successfull.
    </div>
    <div class='form-actions right'>

      <input type='submit' class='btn' value='Create Admin User' />
    </div>
  {/if}
</form>
