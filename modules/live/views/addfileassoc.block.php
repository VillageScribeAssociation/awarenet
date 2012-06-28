<? /*

<h2>File Associations</h2>

<p>The following map file extensions to modules which handle file attachments of these types.  For example the images module can perform operations such as generating thumbnails, and the videos module provides a player for video files attached to some object.  Files which are simply to be stored should be handled by the 'files' module.  If files to not match a known type they will not be accepted by the attachments system.  Note that file extensions are case insensitive and that user permissions are assigned by module.</p>

[[:live::listfileassoc:]]

<p>To add or replace a file assocication, enter the file extension (eg: 'jpg', 'mp4', 'doc') in the box below and select which module handles files of this type.</p>

<form name='frmAddFileAssoc' method='POST' action='%%serverPath%%live/settings/'>
	<input type='hidden' name='action' value='addFileAssociation' />
	<input type='text' name='ext' value='' size='5' />
	[[:admin::selectmodule::varname=module:]]
	<input type='submit' value='Add' />
</form>
<hr/>

*/ ?>
