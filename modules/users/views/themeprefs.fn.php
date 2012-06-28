<?

	require_once($kapenta->installPath . 'modules/users/models/preset.mod.php');

//--------------------------------------------------------------------------------------------------
//|	forms to change theme preferences
//--------------------------------------------------------------------------------------------------
//opt: userUID - UID of a Users_User object (default is current user) [string]

function users_themeprefs($args) {
	global $theme;
	global $user;	

	$userUID = $user->UID;				//%	user to show settings for [string]
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if (('public' == $user->role) || ('banned' == $user->role)) { return ''; }
	if (true == array_key_exists('userUID', $args)) { $userUID = $args['userUID']; }
	if (('admin' != $user->role) && ($userUID != $user->UID)) { return '(not authorized)'; }

	$model = new Users_Preset();
	$model->loadUserTheme($userUID);
	
	if (false == $model->registryLoaded) { return '(could not load user preferences.)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/users/views/themeprefs.block.php');

	$labels = $model->getRegistryKeys();
	$labels['background'] = '';

	if (true == array_key_exists('theme.i.background', $labels)) {
		$parts = explode('/', $labels['theme.i.background']);
		foreach($parts as $part) { $labels['background'] = $part; }
	}

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
