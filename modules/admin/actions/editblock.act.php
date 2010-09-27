<?

//--------------------------------------------------------------------------------------------------
//*	edit a block template
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	// check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); } 
	if ('' == $req->ref) { $page->do404('no reference given'); }
	if (false == array_key_exists('module', $req->args)) { $page->do404('Module not specified.'); }

	//----------------------------------------------------------------------------------------------
	// check the the block exists
	//----------------------------------------------------------------------------------------------
	$module = $req->args['module'];
	$block = $req->ref;
	$fileName = 'modules/' . $module . '/views/' .  $block . '.block.php';

	if (false == $kapenta->fileExists($fileName)) { $page->do404('No such block template.'); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/admin/actions/editblock.page.php');
	$page->blockArgs['xmodule'] = $req->args['module'];
	$page->blockArgs['xblock'] = $req->ref;
	$page->render();

?>
