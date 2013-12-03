<? /*
<h2>Module definition: %%modulename%%</h2>
<form name='editModuleBasic' method='POST' action='%%serverPath%%admin/savemodule/'>
	<input type='hidden' name='action' value='saveModule' />
	<input type='hidden' name='module' value='%%modulename%%' />
	<b>Description:</b><br/>
	<textarea rows='6' cols='80' name='description'>%%description%%</textarea><br/>
	<table noborder>
		<tr>
			<td><b>Version:</b></td>
			<td><input type='text' name='version' value='%%version%%' size='5' /></td>
			<td><b>Revision:</b></td>
			<td><input type='text' name='revision' value='%%revision%%' size='5' /></td>
			<td>&nbsp;<input type='submit' value='Save' /></td>
		</tr>
	</table>
</form>
<hr/>

<h2>Default Permissions</h2>
<form name='editDefaultPermissions' method='POST' action='%%serverPath%%admin/savemodule/'>
	<input type='hidden' name='action' value='saveDefaultPermissions' />
	<input type='hidden' name='module' value='%%modulename%%' />
	<textarea rows='6' cols='80' name='defaultpermissions'>%%defaultpermissions%%</textarea><br/>
	<input type='submit' value='Save' />
</form>
<hr/>
<br/>

[[:theme::navtitlebox::label=Models:]]
%%editmodelforms%%
[[:admin::addmodelform::module=%%modulename%%:]]
<hr/>

*/ ?>
