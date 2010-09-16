<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	short summary of user record, suitable for including in lists (perm:summary)
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of record to summarise [string]

function users_summary($args) {
	global $theme, $user;
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $args)) { return ''; }
	if (false == $user->authHas('users', 'Users_User', 'summary', '')) { return ''; }

	$model = new Users_User($args['UID']);
	if (false == $model->loaded) { return ''; }

	$block = $theme->loadBlock('modules/users/views/summary.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
