<?

//--------------------------------------------------------------------------------------------------
//*	displays a form for managing a given module (install, change settings, etc)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and module
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }		// only admins can do this
	if ('' == $kapenta->request->ref) { $kapenta->page->do404('no module specified'); }
	if (false == $kapenta->moduleExists(strtolower($kapenta->request->ref)))	{ $kapenta->page->do404('no such module'); }

	//----------------------------------------------------------------------------------------------
	//	show the page
	//----------------------------------------------------------------------------------------------
	$loaded = $kapenta->page->load('modules/admin/actions/module.page.php');
	$kapenta->page->blockArgs['modulename'] = strtolower($kapenta->request->ref);
	$kapenta->page->render();

?>
