<?

	require_once($kapenta->installPath . 'modules/code/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a code item
//--------------------------------------------------------------------------------------------------
//reqarg: UID - UID of a Code_File object [string]

	//----------------------------------------------------------------------------------------------
	//	check UID and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $kapenta->request->args)) { $page->do404(); }

	$model = new Code_File($kapenta->request->args['UID']);
	if (false == $model->loaded) { $page->do404(); }
	if (false == $user->authHas('code', 'code_file', 'commit', $model->UID)) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	add confirmation form as a session message and redirect back to item
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/code/views/confirmdelete.block.php');
	$labels = array('UID' => $model->UID, 'raUID' => $model->UID);
	$html = $theme->replaceLabels($labels, $block);
	
	$session->msg($html);
	$page->do302('code/show/' . $model->UID);

?>
