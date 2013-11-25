
//--------------------------------------------------------------------------------------------------
//*	javascript for editing notifications
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//*	Open Javascript popup window for editing a notice
//--------------------------------------------------------------------------------------------------
//arg: noticeUID - UID of a Newsletter_Notice object which has changed [string]

function newsletter_editnotice(noticeUID) {
	kwindowmanager.createWindow(
		'Edit Notice',
		jsServerPath + 'newsletter/editnotice/' + noticeUID,
		570,
		500
	);
}

//--------------------------------------------------------------------------------------------------
//*	Remove a notice from the rendered page
//--------------------------------------------------------------------------------------------------
//arg: noticeUID - UID of a Newsletter_Notice object which has changed [string]

function newsletter_removenotice(noticeUID) {
	var theDiv = document.getElementById('divNotice' + noticeUID);
	theDiv.innerHTML = '';
}

//--------------------------------------------------------------------------------------------------
//*	Open Javascript popup window for editing a subscription
//--------------------------------------------------------------------------------------------------
//arg: subscriptionUID - UID of a Newsletter_Subscription object [string]

function newsletter_editsubscription(subscriptionUID) {
	kwindowmanager.createWindow(
		'Edit Subscription',
		jsServerPath + 'newsletter/editsubscription/' + subscriptionUID,
		570,
		180
	);
}

//--------------------------------------------------------------------------------------------------
//*	AJAX refresh of a notice
//--------------------------------------------------------------------------------------------------
//arg: noticeUID - UID of a Newsletter_Notice object which has changed [string]

function newsletter_reloadnotice(noticeUID) {
	var blockTag = '[[' + ':newsletter::shownotice::noticeUID=' + noticeUID + ':]]';
	klive.removeBlock(blockTag);
	klive.bindDivToBlock('divNotice' + noticeUID, blockTag, false);
}

//--------------------------------------------------------------------------------------------------
//*	AJAX refresh of category lists
//--------------------------------------------------------------------------------------------------

function newsletter_reloadcategories() {
	var blockTag = '[[' + ':newsletter::listcategories:]]';
	klive.removeBlock(blockTag);
	klive.bindDivToBlock('divCategories', blockTag, false);
}


//--------------------------------------------------------------------------------------------------
//*	AJAX refresh of subscriber lists
//--------------------------------------------------------------------------------------------------

function newsletter_reloadsubscriptions() {
	var blockTag = '[[' + ':newsletter::listsubscriptions:]]';
	klive.removeBlock(blockTag);
	klive.bindDivToBlock('divSubscriptions', blockTag, false);
}

