<?

	require_once($kapenta->installPath . 'modules/users/models/preset.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a theme preset
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }
	if (false == array_key_exists('UID', $kapenta->request->args)) { $kapenta->page->do404('UID not given'); }

	$model = new Users_Preset($kapenta->request->args['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Preset not found.'); }

	//----------------------------------------------------------------------------------------------
	//	make confirmation form
	//----------------------------------------------------------------------------------------------
	$labels = array('UID' => $model->UID, 'raUID' => $model->alias);
	$block = $theme->loadBlock('modules/users/views/confirmdeletepreset.block.php');
	$html = $theme->replaceLabels($labels, $block);
	$session->msg($html, 'warn');

	//----------------------------------------------------------------------------------------------
	//	redirect back to post to be deleted
	//----------------------------------------------------------------------------------------------	
	$kapenta->page->do302('users/themepresets/' . $model->alias);

?>
