<form id='uploadImage' style='{$style}' action='{$action}' method='post' enctype='multipart/form-data'>
	<table>
		<tr>
			<th colspan='2'>{$lang_image_upload}</th>
		</tr>
		<tr class='row1'>
			<td class='left'>
				<label for='image'>{$lang_image_choose}</label>
			</td>
			<td class='right'>
				<input type='file' name='image' id='image' />
				<div class='description'>{$lang_image_upload_info}</div>
			</td>
		</tr>
		<tr class='row2'>
			<td class='left'>
				<label for='agreement'>{$lang_image_agreement}</label>
			</td>
			<td class='right'>
				<input type='checkbox' id='agreement' name='agreement' value='1' />
			</td>
		</tr>
	</table>
	<input type='submit' value='{$lang_image_upload}' class='inputbutton' />
	<input type='hidden' value='formdata' name='create_avatar' />
	<input type='hidden' name='MAX_FILE_SIZE' value='409600' />
</form>