<form method='post' action='/Invite'>
	<table>
		<tr>
			<th colspan='2'>{$lang_headline}</th>
		</tr>
		<tr class='row1'>
			<td class='left'>
				<label for='email'>{$lang_email_of_friend}</label>
			</td>
			<td class='right'>
				<input name='email' id='email'
					type='text' class='inputtext' />
			</td>
		</tr>
		<tr class='row2'>
			<td class='left'>
				<label for='content'>{$lang_content}</label>
			</td>
			<td class='right'>
				<textarea name='message' id='content'
					 rows='11' cols='50' disabled>{$message}</textarea>
			</td>
		</tr>
		<tr class='row1'>
			<td class='left'>
				<label for='notes'>{$lang_own_message}</label>
			</td>
			<td class='right'>
				<textarea name='notes' id='notes' rows='8' cols='50'></textarea>
			</td>
		</tr>
	</table>
	<input type='submit' class='inputbutton' value='{$lang_submit}' />
	<input type='hidden' value='formdata' name='invite_friend' />
</form>