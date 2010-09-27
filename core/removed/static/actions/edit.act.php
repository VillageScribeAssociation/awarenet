<?

	require_once($kapenta->installPath . 'modules/home/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a static page
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do302('home/list/'); }
	$UID = $aliases->findRedirect('Home_Static');
	if (false == $user->authHas('home', 'Home_Static', 'edit', $UID))
		{ $page->do403('you are not authorized to edit this page'); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------	
	$page->load('modules/home/actions/edit.page.php');
	$page->blockArgs['UID'] = raGetOwner($req->ref, 'static');
	$page->blockArgs['raUID'] = $req->ref;
	$page->render();
	
?>
