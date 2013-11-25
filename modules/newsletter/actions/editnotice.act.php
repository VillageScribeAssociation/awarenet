<?
	//require_once($kapenta->installPath . 'modules/newsletter/models/notice.mod.php');
	// ^ sometimes needed for breadcrumbs, etc

//--------------------------------------------------------------------------------------------------
//*	show form to edit a Notice object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404('Notice not specified'); }
	$UID = $kapenta->request->ref;
	if (false == $db->objectExists('newsletter_notice', $UID)) { $page->do404(); }
	if (false == $user->authHas('newsletter', 'newsletter_notice', 'edit', $UID))
		{ $page->do403('You are not authorized to edit this Notices.'); }


	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/newsletter/actions/editnotice.page.php');
	$page->requireJs('%%serverPath%%modules/editor/js/HyperTextArea.js');
	$page->requireJs('%%serverPath%%modules/live/js/live.js');
	$kapenta->page->blockArgs['UID'] = $UID;
	$kapenta->page->blockArgs['noticeUID'] = $UID;
	$kapenta->page->render();

?>
