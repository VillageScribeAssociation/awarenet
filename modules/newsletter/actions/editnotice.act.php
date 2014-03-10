<?
	//require_once($kapenta->installPath . 'modules/newsletter/models/notice.mod.php');
	// ^ sometimes needed for breadcrumbs, etc

//--------------------------------------------------------------------------------------------------
//*	show form to edit a Notice object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->page->do404('Notice not specified'); }
	$UID = $kapenta->request->ref;
	if (false == $kapenta->db->objectExists('newsletter_notice', $UID)) { $kapenta->page->do404(); }
	if (false == $user->authHas('newsletter', 'newsletter_notice', 'edit', $UID))
		{ $kapenta->page->do403('You are not authorized to edit this Notices.'); }


	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/newsletter/actions/editnotice.page.php');
	$kapenta->page->requireJs('%%serverPath%%modules/editor/js/HyperTextArea.js');
	$kapenta->page->requireJs('%%serverPath%%modules/live/js/live.js');
	$kapenta->page->blockArgs['UID'] = $UID;
	$kapenta->page->blockArgs['noticeUID'] = $UID;
	$kapenta->page->render();

?>
