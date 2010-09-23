<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	select box for choosing a user's group (sensitive information, only available to admins)
//--------------------------------------------------------------------------------------------------
//arg: default - group the user is currently in, set to 'public' if blank [string]
//opt: varname - html form element name, default is role [string]

function users_selectgroup($args) {
	global $theme, $user;
	$varname = 'role';
	$html = '';
	
	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return false; }
	if (false == array_key_exists('default', $args)) { return ''; }
	if ('' == $args['default']) { $args['default'] == 'public'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$labels = array('default' => $args['default'], 'varname' => $varname);
	$block = $theme->loadBlock('modules/users/views/selectgroup.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
