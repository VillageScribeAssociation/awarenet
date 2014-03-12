<?

	require_once($kapenta->installPath . 'modules/code/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a code item
//--------------------------------------------------------------------------------------------------
//reqarg: UID - UID of a Code_File object [string]

	//----------------------------------------------------------------------------------------------
	//	check UID and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $kapenta->request->args)) { $kapenta->page->do404(); }

	$model = new Code_File($kapenta->request->args['UID']);
	if (false == $model->loaded) { $kapenta->page->do404(); }
	if (false == $kapenta->user->authHas('code', 'code_file', 'commit', $model->UID)) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	add confirmation form as a session message and redirect back to item
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/code/views/confirmdelete.block.php');
	$labels = array('UID' => $model->UID, 'raUID' => $model->UID);
	$html = $theme->replaceLabels($labels, $block);
	
	$kapenta->session->msg($html);
	$kapenta->page->do302('code/show/' . $model->UID);

?>
