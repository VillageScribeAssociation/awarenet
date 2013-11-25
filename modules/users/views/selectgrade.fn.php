<?

//--------------------------------------------------------------------------------------------------
//|	form for choosing a user's grade
//--------------------------------------------------------------------------------------------------
//opt: varname - html form field name [string]
//opt: default - pre-selected value [string]

function users_selectgrade($args) {
	global $theme;
	global $kapenta;

	$html = '';							//%	return value [string]
	$default = '';						//%	default value [string]
	$varname = 'grade';					//%	form field name [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('default', $args)) { $default = $args['default']; }
	if (true == array_key_exists('varname', $args)) { $varname = $args['varname']; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$gradeStr = $kapenta->registry->get('users.grades');
	$grades = explode("\n", $gradeStr);

	$html .= "<select name='$varname'>\n";
	foreach($grades as $grade) {
		$grade = trim($grade);
		if ('' != $grade) {
			$selected = '';
			if ($default == $grade) { $selected = " selected='YES'"; }
			$html .= "\t<option value='$grade'$selected>$grade</option>\n";
		}
	}
	$html .= "</select>\n";

	return $html;
}

?>
