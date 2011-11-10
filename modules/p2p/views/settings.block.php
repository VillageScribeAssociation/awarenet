<? /*

<h2>Basic Settings</h2>

<form name='p2pSettings' method='POST' action='%%serverPath%%p2p/settings/' >
<input type='hidden' name='action' value='changeSettings' />
<table noborder width='100%'>
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
    <td><b>Server Name: </b></td>
    <td><input type='text' name='p2p_server_name' value='%%p2p.server.name%%' size='40' style='width: 100%;' /></td>
  </tr>
  <tr>
    <td><b>Server Url: </b></td>
    <td><input type='text' name='p2p_server_url' value='%%p2p.server.url%%' size='40' style='width: 100%;' /></td>
  </tr>
  <tr>
    <td><b>Server UID: </b></td>
    <td><input type='text' name='p2p_server_uid' value='%%p2p.server.uid%%' size='20' /></td>
  </tr>
  <tr>
    <td><b>Batch Size: </b></td>
    <td>
		<input type='text' name='p2p_batchsize' value='%%p2p.batchsize%%' size='5' />
		<small>Number of database objects to transfer per worker cycle.</small>
	</td>
  </tr>
  <tr>
    <td><b>Batch Parts: </b></td>
    <td>
		<input type='text' name='p2p_batchparts' value='%%p2p.batchparts%%' size='5' />
		<small>Number of file parts to transfer per worker cycle.</small>
	</td>
  </tr>
  <tr>
    <td><b>Firewalled: </b></td>
    <td>
		<select name='p2p_server_fw'>
			<option value='%%p2p.server.fw%%'>%%p2p.server.fw%%</option>
			<option value='yes'>yes</option>
			<option value='no'>no</option>
		</select>
	</td>
  </tr>
  <tr>
    <td></td>
    <td><input type='submit' value='Update Settings &gt;&gt;' /></td>
  </tr>
</table>
</form>
<br/>

<h2>File Upload Times</h2>

<p>This sets the hours when files may be transferred by this peer.  Note that this 
peer will still honor requests from other peers, but it will not push or pull 
files unless the hour matches one below.  Separate hours (0-23) by commas.</p>

<form name='p2pFileSettings' method='POST' action='%%serverPath%%p2p/settings/' >
<input type='hidden' name='action' value='changeSettings' />
<b>Allow file upload at:</b>
<input type='text' name='p2p_filehours' value='%%p2p.filehours%%' style='width: 100%;' />
<input type='submit' value='Update Settings &gt;&gt;' />
</form>

<h2>Public Key</h2>

<pre>%%p2p.server.pubkey%%</pre>

*/ ?>
