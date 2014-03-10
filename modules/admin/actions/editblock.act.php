<?

//--------------------------------------------------------------------------------------------------
//*	edit a block template
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	// check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); } 
	if ('' == $kapenta->request->ref) { $kapenta->page->do404('no reference given'); }
	if (false == array_key_exists('module', $kapenta->request->args)) { $kapenta->page->do404('Module not specified.'); }

	//----------------------------------------------------------------------------------------------
	// check the the block exists
	//----------------------------------------------------------------------------------------------
	$module = $kapenta->request->args['module'];
	$block = $kapenta->request->ref;
	$fileName = 'modules/' . $module . '/views/' .  $block . '.block.php';

	if (false == $kapenta->fs->exists($fileName)) { $kapenta->page->do404('No such block template.'); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/admin/actions/editblock.page.php');
	$kapenta->page->blockArgs['xmodule'] = $kapenta->request->args['module'];
	$kapenta->page->blockArgs['xblock'] = $kapenta->request->ref;
	$kapenta->page->render();

?>
