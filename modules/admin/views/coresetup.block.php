<? /*

<h2>Kapenta Installation</h2>

<p>These settings let the kapenta core know where it is located on the disk (for constructing file 
paths) and on the network (for constructing URLs).  Both should end with a trailing slash.</p>

<form name='csKapentaInstallation' method='POST' action='%%serverPath%%admin/coresetup/'>
  <table noborder>
    <tr>
	  <td><b>installPath:</b></td>
	  <td><input type='text' name='kapenta_installpath' value='%%kapenta.installpath%%' size='30'></td>	
    </tr>
    <tr>
	  <td><b>serverPath:</b></td>
	  <td><input type='text' name='kapenta_serverpath' value='%%kapenta.serverpath%%' size='30'></td>	
    </tr>
    <tr>
	  <td><b></b></td>
	  <td><input type='submit' value='Set'></td>	
    </tr>
  </table>
</form>

<h2>Database Wrapper</h2>

<p>These settings let kapenta know which database to use and how to connect to it.</p>

<form name='csKapentaDb' method='POST' action='%%serverPath%%admin/coresetup/'>
  <table noborder>
    <tr>
	  <td><b>DB Host:</b></td>
	  <td><input type='text' name='kapenta_db_host' value='%%kapenta.db.host%%' size='30'></td>	
    </tr>
    <tr>
	  <td><b>DB Name:</b></td>
	  <td><input type='text' name='kapenta_db_name' value='%%kapenta.db.name%%' size='30'></td>	
    </tr>
    <tr>
	  <td><b>DB User:</b></td>
	  <td><input type='text' name='kapenta_db_user' value='%%kapenta.db.user%%' size='30'></td>	
    </tr>
    <tr>
	  <td><b>DB Password:</b></td>
	  <td><input type='text' name='kapenta_db_password' value='%%kapenta.db.password%%' size='30'></td>	
    </tr>
    <tr>
	  <td><b>Persistent:</b></td>
	  <td>
		<select name='kapenta_db_persistent'>
			<option value='%%kapenta.db.persistent%%'>%%kapenta.db.persistent%%</option>
			<option value='yes'>yes</option>
			<option value='no'>no</option>
		</select> <small>(recommended)</small>
	  </td>	
    </tr>
    <tr>
	  <td><b></b></td>
	  <td><input type='submit' value='Set'></td>	
    </tr>
  </table>
</form>

<h2>Site Preferences</h2>

<p>Default behavior of your kapenta installation.</p>

<form name='csKapentaSite' method='POST' action='%%serverPath%%admin/coresetup/'>
  <table noborder>
    <tr>
	  <td><b>Site Name:</b></td>
	  <td><input type='text' name='kapenta_sitename' value='%%kapenta.sitename%%' size='30'></td>	
    </tr>
    <tr>
	  <td><b>Default Module:</b></td>
	  <td><input type='text' name='kapenta_modules_default' value='%%kapenta.modules.default%%' size='30'></td>	
    </tr>
    <tr>
	  <td><b>Default Theme:</b></td>
	  <td><input type='text' name='kapenta_themes_default' value='%%kapenta.themes.default%%' size='30'></td>	
    </tr>
    <tr>
	  <td><b></b></td>
	  <td><input type='submit' value='Set'></td>	
    </tr>
  </table>
</form>

<h2>Local Subnet</h2>

<p>Clients connecting from outside of you local network can be redirected to a different awareNet instance to conserve
bandwidth.  Clear or leave blank to disable this feature.</p>

<form name='csKapentaSite' method='POST' action='%%serverPath%%admin/coresetup/'>
  <table noborder>
    <tr>
	  <td><b>Redirect to:</b></td>
	  <td><input type='text' name='kapenta_alternate' value='%%kapenta.alternate%%' size='30' /></td>
    </tr>
    <tr>
	  <td><b>Local subnet start:</b></td>
	  <td>
        <input type='text' name='kapenta_snstart' value='%%kapenta.snstart%%' size='10' /> 
		<small>Start of IPv4 range, dotted decimal notation, four octets.</small>
      </td>
    </tr>
    <tr>
	  <td><b>Local subnet end:</b></td>
	  <td>
        <input type='text' name='kapenta_snend' value='%%kapenta.snend%%' size='10' /> 
		<small>End of IPv4 range, dotted decimal notation, four octets.</small>
      </td>
    </tr>
    <tr>
	  <td><b></b></td>
	  <td><input type='submit' value='Set' /></td>
    </tr>
  </table>
</form>

*/ ?>
