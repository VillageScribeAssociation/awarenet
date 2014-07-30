<? /*
<h3>add/edit contact details<a name='topTitle'></a></h3>

<form name='editContact' method='POST' action='/contact/save/'>
<input type='hidden' name='action' value='saveDetail' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder>
  <tr>
    <td><b>type</b></td>
    <td>
      <select name='type'>
	<option value='%%type%%'>%%type%%</option>
	<option value='email address'>email address</option>
	<option value='postal address'>postal address</option>
	<option value='telephone number'>telephone number</option>
	<option value='cell number'>cell number</option>
	<option value='fax number'>fax number</option>
	<option value='geocode'>geocode</option>
	<option value='voicemail number'>voicemail number</option>
	<option value='pager number'>pager number</option>
	<option value='web page'>web page</option>
	<option value='Skype ID'>Skype ID</option>
	<option value='MSN ID'>MSN ID</option>
	<option value='ICQ number'>ICQ number</option>
	<option value='YIM ID'>YIM ID</option>
      </select>
    </td>
  </tr>
  <tr>
    <td><b>description</b></td>
    <td><input type='text' name='description' value='%%description%%' /></td>
  </tr>
  <tr>
    <td><b>contact</b></td>
    <td><textarea name='value' rows='4' cols='60'>%%value%%</textarea></td>
  </tr>
  <tr>
    <td><b></b></td>
    <td>
      <table noborder>
        <tr>
          <td valign=top>
            <input type='submit' value='save' />
            </form>
          </td>	
          <td valign=top>
            <form method='GET' action='%%serverPath%%%%ifUrl%%'>
            <input type='submit' value='cancel' />
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
*/ ?>
