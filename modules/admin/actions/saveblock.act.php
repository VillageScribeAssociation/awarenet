<?

//--------------------------------------------------------------------------------------------------
//*	save submitted block, return user to to /pages/list
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); } // admins only
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('saveBlock' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }
	if (false == array_key_exists('module', $_POST)) { $kapenta->page->do404('Module not specified.'); }
	if (false == array_key_exists('block', $_POST)) { $kapenta->page->do404('Block not specified'); }
	if (false == array_key_exists('blockContent', $_POST)) { $kapenta->page->do404('No content.'); }

	$module = $_POST['module'];
	$block = $_POST['block'];		//TODO: sanitize

	if (false == $kapenta->moduleExists($module)) { $kapenta->page->do404('No such module.'); }

	//----------------------------------------------------------------------------------------------
	//	save the block
	//----------------------------------------------------------------------------------------------
	//TODO: enable editing of theme blocks
	$fileName = 'modules/' . $module . '/views/' . $block . '.block.php';
	$kapenta->fs->put($fileName, stripslashes($_POST['blockContent']));

	$kapenta->page->do302('admin/listpages/#modList' . $_POST['module']);

?>
