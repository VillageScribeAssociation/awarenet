<? /*

<form
	name='editGallery'
	id='frmEditGallery%%UID%%'
	method='POST'
	action='%%serverPath%%/gallery/save/'
	onSubmit="khta.updateAllAreas();"
>

<input type='hidden' name='action' value='saveRecord' />
<input type='hidden' name='UID' value='%%UID%%' />

	<table noborder>
	  <tr>
	    <td><b>Title:</b></td>
	    <td><input type='text' name='title' value='%%galleryTitle%%' size='50' /></td>
	  </tr>
	</table>
	<br/>

	<b>Description:</b> <small>what is this gallery about?</small><br/>
	<div
		class='HyperTextArea64'
		title='description'
		width='-1'
		height='400'
		style='visibility: hidden; display: none'
		refModule='gallery'
		refModel='gallery_gallery'
		refUID='%%UID%%'
	>
	%%description64%%
	</div>

	<script language='Javascript'> khta.convertDivs(); </script>

</form>

<table noborder>
	<tr>
		<td valign='top'>
			<input type='submit' value='Save' onClick="$('#frmEditGallery%%UID%%').submit();" />
		</td>
		<td valign='top'>
			[[:tags::editbutton::refModule=gallery::refModel=gallery_gallery::refUID=%%UID%%:]]
		</td>
		<td valign='top'>
			<form name='cDelete' method='GET' action='%%delUrl%%'>
			<input type='submit' value='Delete' />
			</form>
		</td>
	</tr>
</table>

*/ ?>
