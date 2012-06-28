<? /*

<p>Preset theme customizations allow the same style to be applied to set of users.  To create one, 
customize the appearance of a user account to use as a template, then enter that user's alias in the
box below.</p>

<form name='frmNewPreset' method='POST' action='%%serverPath%%users/newpreset/'>
	<input type='hidden' name='action' value='createPreset' />
	<input type='hidden' name='cat' value='theme' />	

	<b>User:</b> <small>UID or alias of a user account.</small><br/>
	<input type='text' name='userRa' style='width: 100%;' /><br/>

	<b>Title:</b> <small>Name this theme customization.</small><br/>
	<input type='text' name='title' style='width: 100%;' /><br/>

	<b>Description:</b> <small>Also notes, licence and attribution.</small><br/>
	<textarea name='description' rows='5' style='width: 100%;'></textarea>

	<input type='submit' value='Create &gt;&gt;' />
</form>

*/ ?>
