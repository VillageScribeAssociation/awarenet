<?

//--------------------------------------------------------------------------------------------------
//|	makes an HTML select element to choose a user role
//--------------------------------------------------------------------------------------------------
//opt: default - role the user is currently in, set to 'public' if blank [string]
//opt: varname - html form element name, default is role [string]

function users_selectrole($args) {
	global $db;

	$varname = 'role';				//%	html field name [string]
	$default = 'public';			//%	default list item [string]
	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('varname', $args)) { $varname = $args['varname']; }
	if (true == array_key_exists('default', $args)) { $default = $args['default']; }

	//----------------------------------------------------------------------------------------------
	//	load all roles from database
	//----------------------------------------------------------------------------------------------
	$range = $db->loadRange('users_role', '*', '', 'name ASC');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$html .= "<select name='$varname'>\n";

	foreach($range as $item) {
		$selected = '';
		$role = $item['name'];
		if ($role == $default) { $selected = " selected='selected'"; }
		$html .= "\t<option value='$role'$selected>$role</option>\n";
	}

	$html .= "</select>\n";

	return $html;
}

?>
