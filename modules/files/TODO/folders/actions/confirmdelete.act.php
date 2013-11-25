<?

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a folder
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('uid', $kapenta->request->args)) { $page->do404(); }

	$model = new Files_Folder($kapenta->request->args['uid']);
	if (false == $model->loaded) { $page->do404('Folder not found.'); }
	if (false == $user->authHas('files', 'files_folder', 'delete', $model->UID)) { $page->do403(); }
	
	//----------------------------------------------------------------------------------------------
	//	make the block and show the item to be deleted
	//----------------------------------------------------------------------------------------------
	$labels = array('UID' => $kapenta->request->args['uid'], 'raUID' => $groupRa);
	
	$block = $theme->loadBlock('modules/folder/confirmdelete.block.php');
	$html = $theme->replaceLabels($labels, $block);
	
	$session->msg($html, 'warn');
	$page->do302('folder/' . $model->alias);

?>
