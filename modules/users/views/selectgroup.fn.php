<?

//--------------------------------------------------------------------------------------------------
//|	select box for choosing a user's group (sensitive information, only available to admins)
//--------------------------------------------------------------------------------------------------
//opt: default - group the user is currently in, set to 'public' if blank [string]
//opt: varname - html form element name, default is role [string]
//TODO: remove selectgroup.block.php from repository
//TODO: change name to 'selectrole.fn.php'

function users_selectgroup($args) {
	global $theme, $user;
	$varname = 'role';			//%	form element name [string]
	$default = 'student';		//%	default option [string]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return false; }
	if (true == array_key_exists('default', $args)) { $default = $args['default']; }

	//----------------------------------------------------------------------------------------------
	//	get list of roles from database
	//----------------------------------------------------------------------------------------------
	//TODO: this

	$roles = array('student', 'teacher', 'admin', 'banned');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$html = "<select name='$varname'>\n";
	foreach($roles as $role) {
		$selected = '';
		if ($role == $default) { $selected = " selected='selected'"; }
		$html .= "\t<option value='$role'$selected>$role</option>\n";
	}
	$html .= "</select>\n";

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
