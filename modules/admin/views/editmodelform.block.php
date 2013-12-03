<? /*

<h2>Model: %%modelname%%</h2>

<form name='editModel%%modelname%%' method='POST' action='%%serverPath%%admin/savemodel/' >
	<input type='hidden' name='action' value='saveModel'>
	<input type='hidden' name='module'  value='%%modulename%%' />
	<input type='hidden' name='model' value='%%modelname%%' />

	<table noborder>
		<tr>
			<td><b>Name: </b></td>
			<td><input type='text' name='modelname' value='%%modelname%%'></td>
		</tr>
		<tr>
			<td><b>Description: </b></td>
			<td><textarea rows='4' cols='60' name='description'>%%description%%</textarea></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type='submit' value='Update' />
				<input type='button' 
					value='Remove this model.' 
					onClick="document.getElementById('frmRemoveModel%%modelname%%').submit();" 
				/>
			</td>
		</tr>
	</table>
</form>

<h3>Relationships: %%modelname%%</h3>
[[:admin::editrelationshipdefs::modulename=%%modulename%%::modelname=%%modelname%%:]]
[[:admin::addrelationshipform::modulename=%%modulename%%::modelname=%%modelname%%:]]


<h3>Permissions: %%modelname%%</h3>
[[:admin::editpermissiondefs::modulename=%%modulename%%::modelname=%%modelname%%:]]
[[:admin::addpermissionform::modulename=%%modulename%%::modelname=%%modelname%%:]]

<form 
	name='removeModel%%modelname%%' 
	id='frmRemoveModel%%modelname%%' 
	method='POST' 
	action='%%serverPath%%admin/removemodel/'
	>
	<input type='hidden' name='action' value='deleteModel' />
	<input type='hidden' name='module' value='%%modulename%%' />
	<input type='hidden' name='model' value='%%modelname%%' />
</form>
<hr/>

*/ ?>
