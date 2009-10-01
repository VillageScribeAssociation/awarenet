<? /*
<h3>Add New User</h3>
<form name='editUser' method='POST' action='/users/new/'>
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
    <td></td>
    <td><input type='submit' value='Create User' /></td>
  </tr>
</table>
</form>
*/ ?>
