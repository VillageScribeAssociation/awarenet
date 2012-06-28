<?

//--------------------------------------------------------------------------------------------------
//|	make an HTML select element for choosing gallery origin
//--------------------------------------------------------------------------------------------------
//opt: default - default value of select element (user|3rdparty) [string]

function videos_selectorigin($args) {
	global $user;
	global $session;

	$default = 'user';					//%	default origin [string]
	$html = '';							//%	return value [string]

	$values = array(
		'user' => 'Videos created by awareNet users',
		'3rdparty' => '3rd party content used with permission'
	);

	if (true == $session->get('mobile')) {
		$values = array(
			'user' => 'made by awareNet user',
			'3rdparty' => '3rd party (with permission)'
		);
	}

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('default', $args)) { $default = $args['default']; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$html .= "<select name='origin'>\n";
	foreach($values as $origin => $label) {
		$selected = '';
		if ($origin == $default) { $selected = " selected='selected'"; }
		$html .= "\t<option value='$origin'$selected>$label</option>\n";
	}
	$html .= "</select>";

	return $html;
}

?>
