<?
	//require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');
	// ^ sometimes needed for breadcrumbs, etc

//--------------------------------------------------------------------------------------------------
//*	show form to edit a Thread object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	$UID = $aliases->findRedirect('forums_thread');
	if (false == $kapenta->user->authHas('forums', 'forums_thread', 'edit', $UID))
		{ $kapenta->page->do403('You are not authorized to edit this thread.'); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/forums/actions/editthread.page.php');
	$kapenta->page->blockArgs['UID'] = $UID;
	$kapenta->page->blockArgs['threadUID'] = $UID;
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->render();

?>
