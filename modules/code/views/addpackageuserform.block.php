<? /*
<h3>Add a user to this package</h3>
<form name='addCodeUser' method='POST' action='%%serverPath%%code/adduser/'>
<input type='hidden' name='action' value='addPackageUser' />
<input type='hidden' name='packageUID' value='%%UID%%' />

<table noborder>
  <tr>
    <td><b>User:</b></td>
    <td>
		<input type='text' name='user' size='20' /><br/>
		<small>alias or UID of a registered user.</small>
	</td>
  </tr>
  <tr>
    <td><b>Privilege:</b></td>
    <td>
		<select name='privilege'>
			<option value='commit'>commit</option>
		</select>
	</td>
  </tr>
  <tr>
    <td><b></b></td>
    <td><input type='submit' value='Grant &gt;&gt;' /></td>
  </tr>
</table>
</form>
<hr/>
<br/>

*/ ?>
