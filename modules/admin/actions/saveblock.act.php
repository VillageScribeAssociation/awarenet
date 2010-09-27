<?

//--------------------------------------------------------------------------------------------------
//*	save submitted block, return user to to /pages/list
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); } // admins only
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('saveBlock' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('module', $_POST)) { $page->do404('Module not specified.'); }
	if (false == array_key_exists('block', $_POST)) { $page->do404('Block not specified'); }
	if (false == array_key_exists('blockContent', $_POST)) { $page->do404('No content.'); }

	$module = $_POST['module'];
	$block = $_POST['block'];		//TODO: sanitize

	if (false == $kapenta->moduleExists($module)) { $page->do404('No such module.'); }

	//----------------------------------------------------------------------------------------------
	//	save the block
	//----------------------------------------------------------------------------------------------
	//TODO: enable editing of theme blocks
	$fileName = 'modules/' . $module . '/views/' . $block . '.block.php';
	$kapenta->filePutContents($fileName, stripslashes($_POST['blockContent']));

	$page->do302('admin/listpages/#modList' . $_POST['module']);

?>
