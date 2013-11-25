<? /*
[[:theme::navtitlebox::label=Add Chat Server::toggle=divAddServer::hidden=yes:]]
<div id='divAddServer' style='visibility: hidden; display: none;'>
<form name='frmAddServer' method='POST' action='%%serverPath%%chatserver/addpeer/'>
<input type='hidden' name='action' value='addServer' />

<table noborder width='100%'>
  <tr>
	<td><b>UID:</b></td>
	<td><input type='text' name='peerUID' value='' style='width: 100%' /></td>
  </tr>
  <tr>
	<td><b>Name:</b></td>
	<td><input type='text' name='name' value='' style='width: 100%' /></td>
  </tr>
  <tr>
	<td><b>URL:</b></td>
	<td><input type='text' name='URL' value='' style='width: 100%' /></td>
  </tr>
</table>
<b>Public RSA key:</b><br/>
<textarea name='pubkey' rows='5' style='width: 100%'></textarea>
<input type='submit' value='Add Server' />
</form>
</div>

*/ ?>
