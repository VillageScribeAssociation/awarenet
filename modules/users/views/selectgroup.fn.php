<?

	require_once($installPath . 'modules/users/models/friendship.mod.php');
	require_once($installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	select box for choosing a user's group (sensitive information, only available to admins)
//--------------------------------------------------------------------------------------------------
//arg: default - group the user is currently in, set to 'public' if blank [string]

function users_selectgroup($args) {
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }
	if (array_key_exists('default', $args) == false) { return false; }
	if ($args['default'] == '') { $args['default'] == 'public'; }
	$labels = array('default' => $args['default']);
	return replaceLabels($labels, loadBlock('modules/users/views/selectgroup.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>
