<? /*

<script language='Javascript'>
	function Files_EditLicence() {
		$('#tdLicence').show();
		$('#tdAttrib').show();
		$('#tdSource').show();
		kutils.resizeIFrame();
	}
</script>

<div class='indent'>
<form name='editfile' method='POST' action='%%serverPath%%files/save/' >
<input type='hidden' name='action' value='savefile' />
<input type='hidden' name='UID' value='%%UID%%' />
<input type='hidden' name='return' value='%%return%%' />

<table noborder width='100%'>
  <tr>
    <td width='80px'><b>Title:</b></td>
    <td><input type='text' name='title' value='%%title%%' style='width: 100%;' /></td>
  </tr>
  <tr>
    <td><b>Abstract:</b></td>
    <td>
      <textarea name='caption' rows='5' style='width: 100%;'>%%caption%%</textarea></td>
  </tr>
  <tr id='tdLicence' style='display: none;'>
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

      <b>Weight:</b>
      <input type='text' size='5' name='weight' value='%%weight%%' />

    </td>
  </tr>
  <tr id='tdAttrib' style='display: none;'>
    <td><b>Attribution:</b></td>
    <td><input type='text' name='attribName' value='%%attribName%%' style='width: 100%;'/></td>
  </tr>
  <tr id='tdSource' style='display: none;'>
    <td><b>Source URL:</b></td>
    <td><input type='text' name='attribUrl' value='%%attribUrl%%' style='width: 100%;' /></td>
  </tr>
  <tr>
    <td></td>
    <td>
		<input type='submit' value='Save' />
		<a href='javascript:Files_EditLicence();'>[edit licence]</a>
	</td>
  </tr>
</table>
</form>
<!-- %%returnLink%% -->
</div>
*/ ?>
