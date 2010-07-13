<form method='post' action='/Mail/{$id}'>
	<table>
		<tr>
			<th colspan='2'>{$lang_headline}</th>
		</tr>
		<tr class='row1'>
			<td class='left'>
				<label for='email'>{$lang_email}</label>
			</td>
			<td class='right'>
				<input name='email' class='inputtext' id='email'
					value='{$email}' type='text' />
			</td>
		</tr>
		<tr class='row2'>
			<td class='left'>
				<label for='subject'>{$lang_subject}</label>
			</td>
			<td class='right'>
				<input name='subject' class='inputtext' id='subject'
					value='{$subject}' type='text' />
			</td>
		</tr>
		<tr class='row1'>
			<td class='left'>
				<label for='content'>{$lang_content}</label>
			</td>
			<td class='right'>
				<textarea name='content' id='content'
					rows='12' cols='50'>{$content}</textarea>
			</td>
		</tr>
	</table>
	<br />
	<center>
		{$captcha}
	</center>
	<input type='submit' class='inputbutton' value='{$lang_submit}' />
	<input type='hidden' value='formdata' name='send_mail' />
</form>