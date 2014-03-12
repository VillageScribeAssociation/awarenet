<?

	require_once($kapenta->installPath . 'modules/home/models/static.mod.php'); // (?)

//--------------------------------------------------------------------------------------------------
//*	edit a static page
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->page->do302('home/list/'); }
	$UID = $aliases->findRedirect('home_static');
	if (false == $kapenta->user->authHas('home', 'home_static', 'edit', $UID)) 
		{ $kapenta->page->do403('you cannot edit this static page'); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------	
	$kapenta->page->load('modules/home/actions/edit.page.php');
	$kapenta->page->blockArgs['UID'] = $UID;
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->render();
	
?>
