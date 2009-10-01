<? /*
<h1>Edit User: %%username%%</h1>
<form name='editUser' method='POST' action='/users/save/'>
<input type='hidden' name='action' value='saveUserRecord' />
<input type='hidden' name='UID' value='%%UID%%' />
<table noborder>
  <tr>
    <td><b>School:</b></td>
    <td>
        [[:schools::select::default=%%school%%:]] 
        <select name='grade'>
         <option value='%%grade%%'>%%grade%%</option>
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
    <td><input type='text' name='firstname' value='%%firstname%%' /></td>
  </tr>
  <tr>
    <td><b>Surname:</b></td>
    <td><input type='text' name='surname' value='%%surname%%' /></td>
  </tr>
  <tr>
    <td><b>Username:</b></td>
    <td><input type='text' name='username' value='%%username%%' /></td>
  </tr>
  <tr>
    <td><b>Language:</b></td>
    <td><input type='text' name='lang' value='%%lang%%' /></td>
  </tr>
  <tr>
    <td><b>Group:</b></td>
    <td>[[:users::selectgroup::default=%%ofGroup%%:]]</td>
  </tr>
  <tr>
    <td></td>
    <td><input type='submit' value='Save' /></td>
  </tr>
</table>
</form>
<small>UID: %%UID%% recordAlias: %%recordAlias%%</small>
*/ ?>