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
        <select name='grade'>
         <option value='Grade 1'>Grade 1</option>
         <option value='Grade 2'>Grade 2</option>
         <option value='Grade 3'>Grade 3</option>
         <option value='Grade 4'>Grade 4</option>
         <option value='Grade 5'>Grade 5</option>
         <option value='Grade 6'>Grade 6</option>
         <option value='Grade 7'>Grade 7</option>
         <option value='Grade 8'>Grade 8</option>
         <option value='Grade 9'>Grade 9</option>
         <option value='Grade 10'>Grade 10</option>
         <option value='Grade 11'>Grade 11</option>
         <option value='Grade 12'>Grade 12</option>
         <option value='1. Klasse'>1. Klasse</option>
         <option value='2. Klasse'>2. Klasse</option>
         <option value='3. Klasse'>3. Klasse</option>
         <option value='4. Klasse'>4. Klasse</option>
         <option value='5. Klasse'>5. Klasse</option>
         <option value='6. Klasse'>6. Klasse</option>
         <option value='7. Klasse'>7. Klasse</option>
         <option value='8. Klasse'>8. Klasse</option>
         <option value='9. Klasse'>9. Klasse</option>
         <option value='10. Klasse'>10. Klasse</option>
         <option value='11. Klasse'>11. Klasse</option>
         <option value='12. Klasse'>12. Klasse</option>
         <option value='13. Klasse'>13. Klasse</option>
         <option value='Std. 1'>Std. 1</option>
         <option value='Std. 2'>Std. 2</option>
         <option value='Std. 3'>Std. 3</option>
         <option value='Std. 4'>Std. 4</option>
         <option value='Std. 5'>Std. 5</option>
         <option value='Std. 6'>Std. 6</option>
         <option value='Std. 7'>Std. 7</option>
         <option value='Std. 8'>Std. 8</option>
         <option value='Std. 9'>Std. 9</option>
         <option value='Std. 10'>Std. 10</option>
         <option value='Std. 11'>Std. 11</option>
         <option value='Std. 12'>Std. 12</option>
        </select>
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
