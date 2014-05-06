<? /*

<script language='Javascript'>
	function Videos_ShowLicenceForm() {
		$('#linkShowLicence').hide();
		$('#trLicence').show();
		$('#trAttrib').show();
		$('#trSource').show();
		kutils.resizeIFrame();
	}
</script>

<form name='editVideo' method='POST' action='%%serverPath%%videos/savevideo/' >
<input type='hidden' name='action' value='saveVideo' />
<input type='hidden' name='UID' value='%%UID%%' />
<input type='hidden' name='refModule' value='%%refModule%%' />
<input type='hidden' name='refModel' value='%%refModel%%' />
<input type='hidden' name='refUID' value='%%refUID%%' />
<input type='hidden' name='return' value='%%return%%' />

<table noborder width='100%'>
  <tr>
    <td><b>Title:</b></td>
    <td><input type='text' size='30' name='title' value='%%title%%' style='width: 100%;' /></td>
  </tr>
  <tr>
    <td valign='top'><b>Caption:</b></td>
    <td>
      <textarea name='caption' rows='5' cols='47' style='width: 100%;'>%%caption%%</textarea></td>
  </tr>
  <tr>
    <td><b>Availability:</b></td>
    <td>
		[[:videos::selectcategory::default=%%category%%::fieldname=category:]]
	</td>
  </tr>
  <tr id='trLicence' style='display: none;'>
    <td valign='top'><b>Licence:</b></td>
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

      <b>Weight:</b> %%weight%%
    </td>
  </tr>
  <tr id='trAttrib' style='display: none;'>
    <td valign='top'><b>Attribution:    </b></td>
    <td><input type='text' size='30' style='width: 100%;'
         name='attribName' value='%%attribName%%'/></td>
  </tr>
  <tr id='trSource' style='display: none;'>
    <td><b>Source URL:</b></td>
    <td><input type='text' size='30' style='width: 100%;'
         name='attribURL' value='%%attribUrl%%' /></td>
  </tr>
  <tr>
    <td>
		<input type='submit' value='Save' />
		<a id='linkShowLicence' href='javascript:Videos_ShowLicenceForm();'>[edit licence]</a>
	</td>
  </tr>
</table>
</form>
<br/>

*/ ?>
