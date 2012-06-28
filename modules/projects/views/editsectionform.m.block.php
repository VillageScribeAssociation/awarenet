<? /*
<hr/>
<form
	id='frmEditSection%%UID%%'
	name='editSection%%UID%%'
	method='POST'
	action='%%serverPath%%projects/savesection/'
	onSubmit='khta.updateAllAreas();'
>
<input type='hidden' name='action' value='saveSection' />
<input type='hidden' name='UID' value='%%sectionUID%%' />

<h2>%%title%%</h2>

<table noborder>
	<tr>
		<td><b>Title:</b></td>
		<td>
			<input
				type='text'
				name='title%%UID%%'
				id='txtTitle%%UID%%'
				value='%%title%%'
				size='50'
				style='width: 100%;'
			/>
		</td>
	</tr>
</table>
<div
	id='htaSection%%UID%%'
	class='HyperTextArea64'
	title='content%%UID%%'
	width='-1'
	height='400'
	refModule='projects'
	refModel='projects_project'
	refUID='%%projectUID%%'
>%%content64%%</div>
</form>
<table noborder>
	<tr>
		<td valign='top'>
			<input
				type='button'
				value='Save Changes'
				onClick="
					khta.updateAllAreas();				
					project.save(
						'%%UID%%',
						$('#txtTitle%%UID%%').val(),
						$('#txtHtacontent%%UID%%').val()
					);
				"
			/>
		</td>
		<td>
			<input
				type='button'
				value='Cancel'
				onClick="project.cancelEdit('%%UID%%');"
			/>
		</td>
		<td>
			<input
				type='submit'
				value='Delete This Section'
				onClick="project.deleteSection('%%UID%%', '%%title%%');"
			/>
    </td>
 </tr>
</table>
<br/>
<script>
	khta.convertDivs();
</script>
<hr/>
*/ ?>
