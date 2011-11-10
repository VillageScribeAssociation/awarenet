<?

//--------------------------------------------------------------------------------------------------
//|	select video category (field repurposed to control visibility)
//--------------------------------------------------------------------------------------------------
//opt: default - default / preselected item (default is 'user') [string]
//opt: fieldname - name of HTML form field [string]

function videos_selectcategory($args) {
	global $user;
	global $theme;

	$default = 'user';						//%	only logged in users can watch video [string]
	$fieldName = 'category';				//%	HTML form field [string]
	$html = '';								//%	return value [string]

	$values = array(
		'user' => 'Logged in users can watch this video.',
		'private' => 'Logged in users, flash player only.',
		'public' => 'Anyone can watch this video.'
	);

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('default', $args)) { $default = $args['default']; }
	if (true == array_key_exists('fieldname', $args)) { $fieldName = $args['fieldname']; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$html .= "\t<select name='$fieldName'>\n";
	foreach($values as $value => $label) {
		$selected = '';
		if ($default == $value) { $selected = " selected='YES'"; }
		$html .= "\t\t<option value='$value'$selected>$label</option>\n";
	}
	$html .= "\t</select>\n";

	return $html;
}

?>
