<? /*

<h2>Edit Peer</h2>

<form name='addServer' method='POST' action='%%serverPath%%p2p/savepeer/'>
<input type='hidden' name='action' value='savePeer' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder width='100%'>
  <tr>
    <td><b>UID:</b></td>
    <td>%%UID%%</td>
  </tr>
  <tr>
    <td><b>Name:</b></td>
    <td><input type='text' name='name' size='25' value='%%name%%' style='width: 100%;' /></td>
  </tr>
  <tr>
    <td><b>URL:</b></td>
    <td><input type='text' name='url' size='25' value='%%url%%' style='width: 100%;' /></td>
  </tr>
  <tr>
    <td><b>Firewalled:</b></td>
    <td>
		<select name='firewalled'>
			<option value='%%firewalled%%'>%%firewalled%%</option>
			<option value='yes'>yes</option>
			<option value='no'>no</option>
		</select>
	</td>
  </tr>
</table>
<br/>
<b>Public key:</b> <small>(RSA 4096)</small><br/>
<textarea name='pubkey' rows='14' cols='60' style='width: 100%;'>%%pubkey%%</textarea>
<input type='submit' value='Save &gt;&gt;' />
</form>
<br/>

*/ ?>


