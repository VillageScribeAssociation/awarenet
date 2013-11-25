<? /*

<form name='editAdunit%%UID%%' method='POST' action='%%serverPath%%newsletter/saveadunit/'>
    <input type='hidden' name='action' value='saveAdunit' />
    <input type='hidden' name='UID' value='%%UID%%' />
	<table noborder='noboder' width='100%'>
    <tr>
        <td><b>title</b></td>
        <td><input type='text' name='title' value='%%title%%' style='width: 100%;'/></td>
    </tr>
    <tr>
        <td><b>linktext</b></td>
        <td><input type='text' name='linktext' value='%%linktext%%' style='width: 100%;' /></td>
    </tr>
    <tr>
        <td><b>linkurl</b></td>
        <td><input type='text' name='linkurl' value='%%linkurl%%' style='width: 100%;' /></td>
    </tr>
    <tr>
        <td><b>pinned</b></td>
        <td>
			<select name='pinned'>
				<option value='%%pinned%%'>%%pinned%%</option>
				<option value='yes'>yes</option>
				<option value='no'>no</option>
			</select>
		</td>
    </tr>
    <tr>
        <td><b>weight</b></td>
        <td><input type='text' name='weight' value='%%weight%%' size='5' /></td>
    </tr>
    </table>
<b>Ad text:</b><br/>
<textarea name='tagline' style='width: 100%;' rows='5'>%%tagline%%</textarea>
<script language='Javascript'> khta.convertDivs(); </script>
<br/>
</form>
<table noborder>
  <tr>
    <td><input type='button' value='Save' onClick='area.update(); document.editAdunit%%UID%%.submit()'></td>
    <td>
      <form name='cancelAdunit%%UID%%' method='GET' action='%%serverPath%%newsletter/showadunit/%%UID%%'>
        <input type='hidden' name='action' value='deleteAdunit' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Cancel' />
      </form>
    </td>
    <td>
      <form name='cancelAdunit%%UID%%' method='POST' action='%%serverPath%%newsletter/confirmdeleteadunit/'>
        <input type='hidden' name='action' value='deleteAdunit' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Delete' />
      </form>
    </td>
  </tr>
</table>


*/ ?>
