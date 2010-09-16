<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	profile box on users profile page
//--------------------------------------------------------------------------------------------------
//arg: userRA - recordAlias of record to summarise [string]

function users_profile($args) {
	global $theme, $user;
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('userRa', $args) == false) { return ''; }
	$model = new Users_User($args['userRa']);
	if (false == $user->authHas('users', 'Users_User', 'show', $model->UID)) { return ''; }
	if (false == $model->loaded) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/users/views/profile.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
