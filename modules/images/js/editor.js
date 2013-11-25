
//--------------------------------------------------------------------------------------------------
//	helper functions for editing images
//--------------------------------------------------------------------------------------------------

function Images_EditModal(imageUID) {

	var hWnd = kwindowmanager.createWindow(
		'Edit Image',									//	title
		jsServerPath + 'images/edit/' + imageUID,		//	frame URL
		570, 700,										//	size
		'',												//	icon (TODO)
		true											//	modal
	);

}

function Images_EditTags(imageUID) {

	var tagWindowUrl = ''
	 + jsServerPath
	 + 'tags/edittags'
	 + '/refModule_images'
	 + '/refModel_images_image'
	 + '/refUID_' + imageUID;

	//TODO: add an icon
	var hWnd = kwindowmanager.createWindow('Edit Image Tags', tagWindowUrl, 570, 400, '', true);	

}

function Images_MakeDefault(imageUID) {
	var postUrl = jsServerPath + 'images/makedefaultjs/';
	var params = 'UID=' + imageUID;

	var cbFn = function(responseText, status) {
		if ('200' == status) {
			if (Live_ReloadAttachments) { Live_ReloadAttachments(); }
		} else {
			alert(responseText);
		}
	}

	kutils.httpPost(postUrl, params, cbFn);
}

function Images_Delete(imageUID) {
	if (confirm("Delete image?")) {
		var postUrl = jsServerPath + 'images/delete/';
		var params = 'UID=' + imageUID;

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

