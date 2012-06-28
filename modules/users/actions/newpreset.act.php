<?

	require_once($kapenta->installPath . 'modules/users/models/preset.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new preset theme customization
//--------------------------------------------------------------------------------------------------
//postarg: userRa - alias or UID of a Users_User object [string]
//postarg: cat - set to 'theme' [string]

	//----------------------------------------------------------------------------------------------
	//	check POST fields and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	if (false == array_key_exists('userRa', $_POST)) { $page->do404('User not specified'); }

	//----------------------------------------------------------------------------------------------
	//	create the object and load user theme customizations
	//----------------------------------------------------------------------------------------------
	$model = new Users_Preset();
	$check = $model->loadUserTheme($_POST['userRa']);
	$model->cat = 'theme';

	if (false == $check) { $page->do404('Could not load user theme customizations.'); }

	foreach($_POST as $key => $value) {
		switch($key) {
			case 'cat':				$model->cat = $value;							break;
			case 'title':			$model->title = $utils->cleanTitle($value);		break;
			case 'description':		$model->description = $utils->cleanHtml($value);	break;
		}
	}

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was created and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		$session->msg('Created new preset.<br/>', 'ok');
		$page->do302('/users/themepresets/' . $model->alias);
	} else {
		$session->msg('Could not create new preset:<br/>' . $report);
		$page->do302('/users/themepresets/');
	}

?>
