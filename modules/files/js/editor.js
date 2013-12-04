
//--------------------------------------------------------------------------------------------------
//	helper functions for editing files
//--------------------------------------------------------------------------------------------------

function Files_EditModal(fileUID) {

	var hWnd = kwindowmanager.createWindow(
		'Edit File',									//	title
		jsServerPath + 'files/edit/' + fileUID,			//	frame URL
		570, 700,										//	size
		'',												//	icon (TODO)
		true											//	modal
	);

}

function Files_EditTags(fileUID) {

	var tagWindowUrl = ''
	 + jsServerPath
	 + 'tags/edittags'
	 + '/refModule_files'
	 + '/refModel_files_file'
	 + '/refUID_' + fileUID;

	//TODO: add an icon
	var hWnd = kwindowmanager.createWindow('Edit File Tags', tagWindowUrl, 570, 400, '', true);	

}

function Filess_MakeDefault(fileUID) {
	var postUrl = jsServerPath + 'files/makedefaultjs/';
	var params = 'UID=' + fileUID;

	var cbFn = function(responseText, status) {
		if ('200' == status) {
			if (Live_ReloadAttachments) { Live_ReloadAttachments(); }
		} else {
			alert(responseText);
		}
	}

	kutils.httpPost(postUrl, params, cbFn);
}

function Files_Delete(fileUID) {
	if (confirm("Delete file?")) {
		var postUrl = jsServerPath + 'files/delete/';
		var params = 'UID=' + fileUID;

		var cbFn = function(responseText, status) {
			if ('200' == status) {
				if (Live_ReloadAttachments) { Live_ReloadAttachments(); }
			} else {
				alert(responseText);
			}
		}

	kutils.httpPost(postUrl, params, cbFn);		
	}
}
