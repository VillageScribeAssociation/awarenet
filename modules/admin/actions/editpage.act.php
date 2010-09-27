<?

//--------------------------------------------------------------------------------------------------
//*	edit a page
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if (false == array_key_exists('module', $req->args)) { $page->do404('Module not specified.'); }
	if ('' == trim($req->ref)) { $page->do404('Page not specified (reference).'); }

	$fileName = 'modules/' . $req->args['module'] . '/actions/' . $req->ref;
	if (false == $kapenta->fileExists($fileName)) { $page->do404('Page file not found.'); }

	//----------------------------------------------------------------------------------------------
	// TODO: more error checking here (directory traversal, etc)
	//----------------------------------------------------------------------------------------------
	$page->blockArgs['xmodule'] = $req->args['module'];
	$page->blockArgs['xpage'] = $req->ref;
	$page->load('modules/admin/actions/editpage.page.php');
	$page->render();

?>
