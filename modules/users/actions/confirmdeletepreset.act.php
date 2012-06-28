<?

	require_once($kapenta->installPath . 'modules/users/models/preset.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a theme preset
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if (false == array_key_exists('UID', $req->args)) { $page->do404('UID not given'); }

	$model = new Users_Preset($req->args['UID']);
	if (false == $model->loaded) { $page->do404('Preset not found.'); }

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
	$page->do302('users/themepresets/' . $model->alias);

?>
