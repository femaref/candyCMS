<form action='/' method='post' class='form-horizontal'>
  {if $_result_ == 'success'}
  <div>
    Your Installation was successful. Please delete the installation folder and
    click next to login!
  </div>
  {else}
  <div>
    The Admin Account could not be created, you might have to add an Account manually
    using your favourite SQL-Editor (e.g. PhpMySql).
  </div>
  {/if}
  <div class='form-actions right'>
    <input type='submit' class='btn' value='Your New Installation' />
  </div>
</form>