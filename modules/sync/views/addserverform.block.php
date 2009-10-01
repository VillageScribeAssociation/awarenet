<? /*
[[:theme::navtitlebox::label=New Server:]]
<form name='addServer' method='POST' action='%%serverPath%%sync/addserver/'>
<input type='hidden' name='action' value='addNewServer' />

<table noborder>
  <tr>
    <td><b>Name:</b></td>
    <td><input type='text' name='servername' size='25' /></td>
  </tr>
  <tr>
    <td><b>URL:</b></td>
    <td><input type='text' name='serverurl' size='25' /></td>
  </tr>
  <tr>
    <td><b>Password:</b></td>
    <td><input type='password' name='password' size='25' /></td>
  </tr>
  <tr>
    <td><b>Type:</b></td>
    <td>
      <select name='direction'>
	    <option value='downstream'>downstream</option>
	    <option value='upstream'>upstream</option>
	    <option value='self'>self</option>
      </select>
    </td>
  </tr>
  <tr>
    <td><b>State:</b></td>
    <td>
      <select name='active'>
	    <option value='active'>active</option>
	    <option value='inactive'>inactive</option>
      </select>
    </td>
  </tr>
  <tr>
    <td><b></b></td>
    <td><input type='submit' value='Add Server >>' /></td>
  </tr>
</table>
</form>
<br/>

*/ ?>
