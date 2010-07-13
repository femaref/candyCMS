<form method='post' action='{$action}'>
	<table>
		<tr>
			<th colspan='2'>{$lang_headline}</th>
		</tr>
		<tr class='row1'>
			<td class='left'>
				<label for='title'>{$lang_title}</label>
			</td>
			<td class='right'>
				<input name='title' value='{$title}' type='text'
					id='title' class='inputtext' />
			</td>
		</tr>
		<tr class='row2'>
			<td class='left'>
				<label for='tags'>{$lang_description}</label>
			</td>
			<td class='right'>
				<input name='description' value='{$description}' type='text'
					id='tags' class='inputtext' />
			</td>
		</tr>
	</table>
	<input type='submit' class='inputbutton' value='{$lang_submit}' />
	{if $smarty.get.action == 'update'}
		<input type='reset' class='inputbutton' value='{$lang_reset}' />
		<input type='button' class='inputbutton' value='{$lang_destroy_entry}' style='color:red'
			onclick="confirmDelete('{$title}', '/Gallery/destroy/{$id}')" />
	{/if}
	<input type='hidden' value='{$id}' name='id' />
	<input type='hidden' value='formdata' name='{$formdata}' />
</form>