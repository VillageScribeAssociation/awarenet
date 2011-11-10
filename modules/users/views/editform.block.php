<? /*
<h1>Edit User: %%username%%</h1>
<form name='editUser' method='POST' action='%%serverPath%%users/save/'>
<input type='hidden' name='action' value='saveUserRecord' />
<input type='hidden' name='UID' value='%%UID%%' />
<table noborder>
  <tr>
    <td><b>School:</b></td>
    <td>
        [[:schools::select::default=%%school%%:]] 
        [[:users::selectgrade::default=%%grade%%:]]
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
    <td>[[:users::selectrole::default=%%role%%:]]</td>
  </tr>
  <tr>
    <td></td>
    <td><input type='submit' value='Save' /></td>
  </tr>
</table>
</form>
<div class='inlinequote'><b>Note:</b> Before changing a user's role to teacher the account
must have a telephone number.</div>
<small>UID: %%UID%% recordAlias: %%alias%%</small>
*/ ?>
