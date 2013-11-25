<? /*

<h2>Add Peer</h2>
<form name='addServer' method='POST' action='%%serverPath%%p2p/newpeer/'>
<input type='hidden' name='action' value='newPeer' />
<table noborder width='100%'>
  <tr>
    <td><b>UID:</b></td>
    <td><input type='text' name='UID' size='20' style='width: 100%;' /></td>
  </tr>
  <tr>
    <td><b>Name:</b></td>
    <td><input type='text' name='name' size='20' style='width: 100%;' /></td>
  </tr>
  <tr>
    <td><b>URL:</b></td>
    <td><input type='text' name='url' size='20' style='width: 100%;' /></td>
  </tr>
  <tr>
    <td><b>Firewalled:</b></td>
    <td>
		<select name='firewalled'>
			<option value='yes'>yes</option>
			<option value='no'>no</option>
		</select>
	</td>
  </tr>
</table>
<br/>
<b>Public Key:</b> <small>(RSA 4096)</small>
<textarea name='pubkey' rows='5' cols='20' style='width: 100%;'></textarea>

<input type='submit' value='Add Peer >>' />
</form>

*/ ?>
