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
	if (false == $user->authHas('forums', 'forums_thread', 'edit', $UID))
		{ $page->do403('You are not authorized to edit this thread.'); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/forums/actions/editthread.page.php');
	$page->blockArgs['UID'] = $UID;
	$page->blockArgs['threadUID'] = $UID;
	$page->blockArgs['raUID'] = $req->ref;
	$page->render();

?>
