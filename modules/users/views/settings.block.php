<? /*

<form name='frmUserSettings' method='POST' action='%%serverPath%%users/settings/'>
	<b>Allow registrations from the public: </b>
	<select name='users_allowpublicsignup'>
		<option value='%%users.allowpublicsignup%%'>%%users.allowpublicsignup%%</option>
		<option value='yes'>yes</option>
		<option value='no'>no</option>
	</select>
	<br/>

	<b>Allow teachers to administer users: </b>
	<select name='users_allowteachersignup'>
		<option value='%%users.allowteachersignup%%'>%%users.allowteachersignup%%</option>
		<option value='yes'>yes</option>
		<option value='no'>no</option>
	</select>
	<br/>

	<input type='submit' value='Save Settings'>
</form>
<hr/>

<h2>Grades</h2>
<p>Grades to which users may belong, one per line.</p>
<form name='frmUserGrade' method='POST' action='%%serverPath%%users/settings/'>
<textarea name='users_grades' rows='10' cols='80'>%%users.grades%%</textarea>
<input type='submit' value='Save Settings' />
</form>
<hr/>

*/ ?>
