<form method='post' action='/Login'>
	<fieldset>
		<legend>{$lang_login}</legend>
		<label for='email'>{$lang_email}:</label>
		<input name='email' type='text' id='email' value='' />
		&nbsp;
		<label for='password'>{$lang_password}:</label>
		<input name='password' type='password' id='password' value='' />
	</fieldset>
	<input type='submit' class='inputbutton' value='{$lang_login}' />
	<input type='hidden' value='formdata' name='create_session' />
	<p><a href='/Login/createnewpassword'>Passwort vergessen?</a></p>
</form>