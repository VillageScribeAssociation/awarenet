<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	select box for choosing a user's group (sensitive information, only available to admins)
//--------------------------------------------------------------------------------------------------
//arg: default - group the user is currently in, set to 'public' if blank [string]

function users_selectgroup($args) {
	global $theme;

	global $user;
	if ('admin' != $user->role) { return false; }
	if (array_key_exists('default', $args) == false) { return false; }
	if ($args['default'] == '') { $args['default'] == 'public'; }
	$labels = array('default' => $args['default']);
	return $theme->replaceLabels($labels, $theme->loadBlock('modules/users/views/selectgroup.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>