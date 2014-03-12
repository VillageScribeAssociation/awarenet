<?

	require_once($kapenta->installPath . 'modules/users/models/preset.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a theme preset
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }
	
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('Preset not given.'); }

	$model = new Users_Preset($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Preset not found.'); }


	//----------------------------------------------------------------------------------------------
	//	OK, delete it and redirect back to presets listing
	//----------------------------------------------------------------------------------------------
	$check = $model->delete();
	if (true == $check) { 
		$kapenta->session->msg("Deleted preset: " . $model->title . " (" . $model->UID . ")", 'ok');
	} else {
		$msg = "Could not delete preset: " . $model->title ." (" . $model->UID . ")";
		$kapenta->session->msg($msg, 'bad');
	}

	$kapenta->page->do302('users/themepresets/');

?>
