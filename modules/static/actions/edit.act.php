<?

	require_once($kapenta->installPath . 'modules/home/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a static page
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do302('home/list/'); }
	$UID = $aliases->findRedirect('Home_Static');
	if (false == $user->authHas('home', 'Home_Static', 'edit', $UID))
		{ $page->do403('you are not authorized to edit this page'); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------	
	$kapenta->page->load('modules/home/actions/edit.page.php');
	$kapenta->page->blockArgs['UID'] = raGetOwner($kapenta->request->ref, 'static');
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->render();
	
?>
