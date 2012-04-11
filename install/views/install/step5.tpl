<form action='/sessions/create' method='post' class='form-horizontal'>
  {if $_result_ == 'success'}
    <div class='alert alert-success'>
      <h4 class='alert-heading'>
        Congratulations!
      </h4>
      <p>
        Your installation was successful. You can now delete the install folder and
        <a href='/sessions/create'>login</a>!
      </p>
    </div>
  {else}
    <div class='alert alert-danger'>
      <h4 class='alert-heading'>
        Ooops!
      </h4>
      <p>
        The admin account could not be created. Please restart the installation.
      </p>
    </div>
  {/if}
  <div class='form-actions right'>
    <input type='submit' class='btn' value='Login &rarr;' />
  </div>
</form>