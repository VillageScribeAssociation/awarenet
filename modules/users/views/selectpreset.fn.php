<?

//--------------------------------------------------------------------------------------------------
//|	makes an HTML select element for choosing a theme preset
//--------------------------------------------------------------------------------------------------
//opt: varname - name of HTML field [string]
//opt: default - preselected item, UID of a Users_Preset object [string]

function users_selectpreset($args) {
	global $user;
	global $theme;
	global $db;

	$default = '';				//%	default value of this select element [string]
	$varname = 'preset';		//%	HTML form field name [string]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if (('public' == $user->role) || ('banned' == $user->role)) { return ''; }

	if (true == array_key_exists('varname', $args)) { $varname = $args['varname']; }
	if (true == array_key_exists('default', $args)) { $default = $args['default']; }
	//TODO: user permissions for this, and sanitize args

	//----------------------------------------------------------------------------------------------
	//	make the select element
	//----------------------------------------------------------------------------------------------
	$range = $db->loadRange('users_preset', '*', '', 'title ASC');

	$html .= "<select name='$varname'>";
	foreach($range as $item) {
		$sel = '';
		if ($item['UID'] == $default) { $sel = " selected='selected'"; }
		$html .= "\t<option value='" . $item['UID'] . "'$sel>" . $item['title'] . "</option>\n";
	}
	$html .= "</select>";

	return $html;
}


?>
