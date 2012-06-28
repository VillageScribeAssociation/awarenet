<? /*

[[:theme::navtitlebox::label=Apply To Grade:]]
<div>
<form name='frmPresetUser' method='POST' action='%%serverPath%%users/applypreset/'>
	<input type='hidden' name='to' value='grade' />
	<table noborder width='100%'>
		<tr>
			<td><b>Preset:</b></td>
			<td>[[:users::selectpreset::varname=preset:]]</td>
		</tr>
		<tr>
			<td><b>School</b></td>
			<td>[[:schools::select::varname=school:]]</td>
		</tr>
		<tr>
			<td><b>Grade</b></td>
			<td>[[:users::selectgrade::varname=grade:]]</td>
		</tr>
		<tr>
			<td><b></b></td>
			<td><input type='submit' value='Apply &gt;&gt;' /></td>
		</tr>
	</table>
</form>
</div>
<div class='foot'></div>
<br/>

*/ ?>
