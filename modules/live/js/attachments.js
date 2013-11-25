
//--------------------------------------------------------------------------------------------------
//*	utility javascript to reload all attachments on the page
//--------------------------------------------------------------------------------------------------

function Live_ReloadAttachments() {
	$('.attachmentsnav').each(
		function() {
			
			var block = ''
			 + '[' + '[:live::listattachmentsnav'
			 + '::refModule=' + $(this).attr('refModule')
			 + '::refModel=' + $(this).attr('refModel')
			 + '::refUID=' + $(this).attr('refUID')
			 + '::display=' + $(this).attr('kdisplay')
			 + ':]]';

			klive.removeBlock(block);
			klive.bindDivToBlock($(this).attr('id'), block);

		}
	);
}

function Live_FindAttachmentsModal(hta, displayset) {
	var hWnd = kwindowmanager.createWindow(
		'Add Media to Text',
		jsServerPath + 'tags/insert/hta_' + hta + '/display_' + displayset,
		570, 400,
		jsServerPath + 'themes/clockface/images/icons/abuse2.png',
		true
	);
	
	//kwindowmanager.windows[hWnd].setBanner('Add Media by Tag');
}
