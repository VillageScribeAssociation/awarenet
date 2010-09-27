<?

//--------------------------------------------------------------------------------------------------
//*	displays a form for managing a given module (install, change settings, etc)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and module
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }		// only admins can do this
	if ('' == $req->ref) { $page->do404('no module specified'); }
	if (false == $kapenta->moduleExists(strtolower($req->ref)))	{ $page->do404('no such module'); }

	//----------------------------------------------------------------------------------------------
	//	show the page
	//----------------------------------------------------------------------------------------------
	$loaded = $page->load('modules/admin/actions/module.page.php');
	$page->blockArgs['modulename'] = strtolower($req->ref);
	$page->render();

?>
