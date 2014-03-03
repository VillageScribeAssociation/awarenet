<? /*

<form name='editPackage' method='POST' action='%%serverPath%%packages/savepackage/' />
  <input type='hidden' name='action' value='savePackage' />
  <input type='hidden' name='UID' value='%%UID%%' />
  <table noborder>
	<tr>
		<td><b>Username:</b></td>
		<td>
			<input type='text' name='username' value='%%username%%' size='18' />
			<small>(repository account for commits)</small>
		</td>
	</tr>
	<tr>
		<td><b>Password:</b></td>
		<td>
			<input type='password' name='password' value='%%password%%' size='18' />
			<small>(repository account for commits)</small>
		</td>
	</tr>
	<tr>
		<td><b>Install Script:</b></td>
		<td>
			<input type='text' name='installFile' value='%%installFile%%' size='30' />
		</td>
	</tr>
	<tr>
		<td><b>Install Function:</b></td>
		<td>
			<input type='text' name='installFn' value='%%installFn%%' size='30' />
		</td>
	</tr>
  </table>
  <b>Includes:</b><br/>
  <textarea name='includes' rows='3' cols='80'>%%includes%%</textarea><br/>
  <br/>

  <b>Excludes:</b><br/>
  <textarea name='excludes' rows='3' cols='80'>%%excludes%%</textarea><br/>
  <br/>

  <input type='submit' name='Save' />
</form>

*/ ?>
