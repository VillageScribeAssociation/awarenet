<?

	require_once($installPath . 'modules/users/models/friendships.mod.php');
	require_once($installPath . 'modules/users/models/users.mod.php');

//--------------------------------------------------------------------------------------------------
//	select box for choosing a user's group (sensitive information, only available to admins)
//--------------------------------------------------------------------------------------------------
// * $args['default'] = group the user is currently in, set to 'public' of blank

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