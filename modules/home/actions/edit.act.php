<?

	require_once($kapenta->installPath . 'modules/home/models/static.mod.php'); // (?)

//--------------------------------------------------------------------------------------------------
//*	edit a static page
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do302('home/list/'); }
	$UID = $aliases->findRedirect('home_static');
	if (false == $user->authHas('home', 'home_static', 'edit', $UID)) 
		{ $page->do403('you cannot edit this static page'); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------	
	$page->load('modules/home/actions/edit.page.php');
	$page->blockArgs['UID'] = $UID;
	$page->blockArgs['raUID'] = $req->ref;
	$page->render();
	
?>
