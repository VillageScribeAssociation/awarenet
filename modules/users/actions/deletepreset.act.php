<?

	require_once($kapenta->installPath . 'modules/users/models/preset.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a theme preset
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	
	if (false == array_key_exists('UID', $_POST)) { $page->do404('Preset not given.'); }

	$model = new Users_Preset($_POST['UID']);
	if (false == $model->loaded) { $page->do404('Preset not found.'); }


	//----------------------------------------------------------------------------------------------
	//	OK, delete it and redirect back to presets listing
	//----------------------------------------------------------------------------------------------
	$check = $model->delete();
	if (true == $check) { 
		$session->msg("Deleted preset: " . $model->title . " (" . $model->UID . ")", 'ok');
	} else {
		$msg = "Could not delete preset: " . $model->title ." (" . $model->UID . ")";
		$session->msg($msg, 'bad');
	}

	$page->do302('users/themepresets/');

?>
