<?

//--------------------------------------------------------------------------------------------------
//*	edit a page
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }
	if (false == array_key_exists('module', $kapenta->request->args)) { $kapenta->page->do404('Module not specified.'); }
	if ('' == trim($kapenta->request->ref)) { $kapenta->page->do404('Page not specified (reference).'); }

	$fileName = 'modules/' . $kapenta->request->args['module'] . '/actions/' . $kapenta->request->ref;
	if (false == $kapenta->fs->exists($fileName)) { $kapenta->page->do404('Page file not found.'); }

	//----------------------------------------------------------------------------------------------
	// TODO: more error checking here (directory traversal, etc)
	//----------------------------------------------------------------------------------------------
	$kapenta->page->blockArgs['xmodule'] = $kapenta->request->args['module'];
	$kapenta->page->blockArgs['xpage'] = $kapenta->request->ref;
	$kapenta->page->load('modules/admin/actions/editpage.page.php');
	$kapenta->page->render();

?>
