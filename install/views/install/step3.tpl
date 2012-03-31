<form action='?action=install&step=4' method='post' class='form-horizontal'>
  <div class='control-group {if isset($error.name)}error{/if}'>
    <label for='input-name' class='control-label'>
      Name <span title='{$lang.global.required}'>*</span>
    </label>
    <div class='controls'>
      <input class='focused span4' name='name'
             value='{$name}' type='text' id='input-name' autofocus required />
      {if isset($error.name)}<span class='help-inline'>{$error.name}</span>{/if}
    </div>
  </div>
  <div class='control-group{if isset($error.surname)} alert alert-error{/if}'>
    <label for='input-surname' class='control-label'>
      Surname</span>
    </label>
    <div class='controls'>
      <input class='span4' name='surname' value='{$surname}' id='input-surname' type='text' />
    </div>
  </div>
  <div class='control-group{if isset($error.email)} alert alert-error{/if}'>
    <label for='input-email' class='control-label'>
      Email <span title='{$lang.global.required}'>*</span>
    </label>
    <div class='controls'>
      <input class='span4' name='email' value='{$email}' type='email' id='input-email' required />
      {if isset($error.email)}<span class='help-inline'>{$error.email}</span>{/if}
    </div>
  </div>
  <div class='control-group{if isset($error.password)} alert alert-error{/if}'>
    <label for='input-password' class='control-label'>
      Password <span title='{$lang.global.required}'>*</span>
    </label>
    <div class='controls'>
      <input class='span4' name='password' type='password' id='input-password' required />
      {if isset($error.password)}<span class='help-inline'>{$error.password}</span>{/if}
    </div>
  </div>
  <div class='control-group' id='js-password'>
    <label for='input-password2' class='control-label'>
      Repeat password <span title='{$lang.global.required}'>*</span>
    </label>
    <div class='controls'>
      <input class='span4' name='password2' type='password' id='input-password2' required />
    </div>
  </div>
  <div class='form-actions right'>
    <input type='submit' class='btn' value='Install admin user' />
  </div>
</form>