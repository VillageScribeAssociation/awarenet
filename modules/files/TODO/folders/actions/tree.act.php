<?

//--------------------------------------------------------------------------------------------------
//*	list all galleries in root (NOT YET USED IN AWARENET)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	// check permissions and reference
	//----------------------------------------------------------------------------------------------
	//if (false == $kapenta->user->authHas('files', 'files_folder', 'show', 'TODO:UIDHERE'))
	//	{ $kapenta->page->do403(); }

	if ('' == $kapenta->request->ref) { $kapenta->request->ref = $kapenta->user->alias; }
	$UID = $aliases->findRedirect('users_user');			//TODO: will this work on this module?
	
	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/folders/actions/tree.page.php');		
	$kapenta->page->blockArgs['userUID'] = $UID;								
	$kapenta->page->render();													

?>
