<? /*

<script language='Javascript'>
	function Images_ShowLicenceFields() {
		$('#linkShowLF').hide();
		$('#trLicence').show();
		$('#trAttrib').show();
		$('#trSource').show();
		kwnd.onResize();
	}
</script>

<form name='editImage' method='POST' action='%%serverPath%%images/save/' >
<input type='hidden' name='action' value='saveImage' />
<input type='hidden' name='UID' value='%%UID%%' />
<input type='hidden' name='refModule' value='%%refModule%%' />
<input type='hidden' name='refModel' value='%%refModel%%' />
<input type='hidden' name='refUID' value='%%refUID%%' />
<input type='hidden' name='return' value='%%return%%' />

<table noborder width='100%'>
  <tr>
    <td valign='top'><b>Title:</b></td>
    <td><input type='text' size='40' name='title' value='%%title%%' style='width: 100%;' /></td>
  </tr>
  <tr>
    <td valign='top'><b>Caption:</b></td>
    <td><textarea name='caption' rows='5' cols='40' style='width: 100%;'>%%caption%%</textarea></td>
  </tr>
  <tr id='trLicence' style='display: none;'>
    <td><b>Licence:</b></td>
    <td>
      <select name='licence'>
        <option value='%%licence%%'>%%licence%%</option>
        <option value='Copyright'>Copyright</option>
        <option value='Public Domain'>Public Domain</option>
        <option value='GNU-GPL'>GNU-GPL</option>
        <option value='GNU-LGPL'>GNU-LGPL</option>
        <option value='AFL'>AFL</option>
        <option value='GFDL'>GFDL</option>
        <option value='CC-BY-NC-SA'>CC-BY-NC-SA</option>
        <option value='CC-BY-NC-ND'>CC-BY-NC-ND</option>
        <option value='CC-BY-SA'>CC-BY-SA</option>
        <option value='CC-BY-ND'>CC-BY-ND</option>
      </select>
    </td>
  </tr>
  <tr id='trAttrib' style='display: none;'>
    <td><b>Attribution:</b></td>
    <td><input type='text' size='40' name='attribName' value='%%attribName%%' style='width: 100%;' /></td>
  </tr>
  <tr id='trSource' style='display: none;'>
    <td><b>Source URL:</b></td>
    <td><input type='text' size='40' name='attribURL' value='%%attribUrl%%' style='width: 100%;' /></td>
  </tr>
  <tr>
    <td><b></b></td>
    <td>
		<table noborder>
		  <tr>
			<td><input type='submit' value='Save' /></td>
			<td><a id='linkShowLF' href='javascript:Images_ShowLicenceFields();'>[edit licence]</a></td>
			<td>%%editTagsLink%%</td>
		  </tr>
		</table>
	</td>
  </tr>
</table>
<br/>
</form>
<br/>
%%returnLink%%
*/ ?>
