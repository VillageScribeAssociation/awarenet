<? /*

<h2>Basic Settings</h2>

<form name='p2pSettings' method='POST' action='%%serverPath%%p2p/settings/' >
<input type='hidden' name='action' value='changeSettings' />
<table noborder>
  <tr>
    <td><b>Enabled: </b></td>
    <td>
		<select name='p2p_enabled'>
			<option value='%%p2p.enabled%%'>%%p2p.enabled%%</option>
			<option value='yes'>yes</option>
			<option value='no'>no</option>
		</select>
	</td>
  </tr>
  <tr>
    <td><b>Server UID: </b></td>
    <td><input type='text' name='p2p_server_uid' value='%%p2p.server.uid%%' size='40' /></td>
  </tr>
  <tr>
    <td><b>Server Name: </b></td>
    <td><input type='text' name='p2p_server_name' value='%%p2p.server.name%%' size='40' /></td>
  </tr>
  <tr>
    <td><b>Server Url: </b></td>
    <td><input type='text' name='p2p_server_url' value='%%p2p.server.name%%' size='40' /></td>
  </tr>
  <tr>
    <td></td>
    <td><input type='submit' value='Update Settings &gt;&gt;' /></td>
  </tr>
</table>
</form>
<br/>

<h2>Set/Change Password</h2>

<form name='p2pPassword' method='POST' action='%%serverPath%%p2p/settings/' />
<b>Password:</b> <input type='password' name='p2p_server_password' size='30' />
<input type='submit' value='Change Password &gt;&gt;' />
</form>

*/ ?>
