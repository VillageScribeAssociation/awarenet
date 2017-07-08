
//--------------------------------------------------------------------------------------------------
//	helper functions for editing videos
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//	create a modal window to edit video details
//--------------------------------------------------------------------------------------------------

function Videos_EditModal(videoUID) {

	var hWnd = kwindowmanager.createWindow(
		'Edit Video',									//	title
		jsServerPath + 'videos/editvideo/' + videoUID,	//	frame URL
		570, 700,										//	size
		'',												//	icon (TODO)
		true											//	modal
	);

}

function Videos_EditTags(videoUID) {

	var tagWindowUrl = ''
	 + jsServerPath
	 + 'tags/edittags'
	 + '/refModule_videos'
	 + '/refModel_videos_video'
	 + '/refUID_' + videoUID;

	//TODO: add an icon
	var hWnd = kwindowmanager.createWindow('Edit Video Tags', tagWindowUrl, 570, 400, '', true);	

}

function Videos_EditThumbs(videoUID) {

	var imgWindowUrl = ''
	 + jsServerPath
	 + 'tags/edittags'
	 + '/refModule_videos'
	 + '/refModel_videos_video'
	 + '/refUID_' + videoUID;

	//TODO: add an icon
	var hWnd = kwindowmanager.createWindow(
		'Edit Video Thumbnails',
		imgWindowUrl,
		570, 400, '', true
	);	
	
}

function Videos_MakeDefault(videoUID) {
	var postUrl = jsServerPath + 'videos/makedefaultjs/';
	var params = 'UID=' + videoUID;

	var cbFn = function(responseText, status) {
		if ('200' == status) {
			if (Live_ReloadAttachments) { Live_ReloadAttachments(); }
		} else {
			alert(responseText);
		}
	}

	kutils.httpPost(postUrl, params, cbFn);
}

function Videos_Delete(videoUID) {
	if (confirm("Delete video?")) {
		alert('deleting video: ' + videoUID);
		var postUrl = jsServerPath + 'videos/delete/';
		var params = 'UID=' + videoUID;

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

