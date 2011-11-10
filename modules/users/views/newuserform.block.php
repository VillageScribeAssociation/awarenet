<? /*
<script language='Javascript'>
	function users_checkNewForm() {
		var report = '';
		var theForm = document.getElementById('addNewUserForm');
		if (('teacher' == theForm.role.value) && ('' == trim(theForm.tel.value))) {
			report = report + 'Please enter a contact telephone number for this teacher.<br/>';
		}

		var theDiv = document.getElementById('newUserFormErrorMsg');
		theDiv.innerHTML = "<span class='ajaxerror'>" + report + "</span>";

		if ('' == report) { theForm.submit(); } 
	}
</script>

<h3>Add New User</h3>
<form name='editUser' id='addNewUserForm' method='POST' action='%%serverPath%%users/new/'>
<input type='hidden' name='action' value='newUserRecord' />
<table noborder>
  <tr>
    <td><b>School:</b></td>
    <td>[[:schools::select::default=:]]</td>
  </tr>
  <tr>
    <td><b>Grade</b></td>
    <td>
       [[:users::selectgrade::default=%%grade%%:]]
	</td>
  </tr>
  <tr>
    <td><b>Forename:</b></td>
    <td><input type='text' name='firstname' value='' /></td>
  </tr>
  <tr>
    <td><b>Surname:</b></td>
    <td><input type='text' name='surname' value='' /></td>
  </tr>
  <tr>
    <td><b>Username:</b></td>
    <td><input type='text' name='username' value='' /></td>
  </tr>
  <tr>
    <td><b>Password:</b></td>
    <td><input type='password' name='password' value='' /></td>
  </tr>
  <tr>
    <td><b>Group:</b></td>
    <td>[[:users::selectgroup::default=student:]]</td>
  </tr>
  <tr>
    <td><b>Telephone:*</b></td>
    <td><input type='text' size='20' name='tel' value='' /></td>
  </tr>
  <tr>
    <td><b>Email:</b></td>
    <td><input type='text' size='20' name='email' value='' /></td>
  </tr>
  <tr>
    <td></td>
    <td>
		<div id='newUserFormErrorMsg'></div>
		<input type='button' value='Create User' onClick='users_checkNewForm();' />
	</td>
  </tr>
</table>
<br/>
<small>* A telephone number is required to create teacher accounts.</small>
</form>
*/ ?>
