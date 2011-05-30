<?

//--------------------------------------------------------------------------------------------------
//|	form for awarding badges to users from their profile
//--------------------------------------------------------------------------------------------------
//opt: varname - name of HTML form field [string]

function badges_select($args) {
	global $db, $user, $theme;
	$html = '';				//%	return value [string]
	$varName = 'badgeUID';	//%	select field name [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (true == array_key_exists('varname', $args)) { $varName = $args['varName']; }

	//----------------------------------------------------------------------------------------------
	//	query the database
	//----------------------------------------------------------------------------------------------
	$range = $db->loadRange('badges_badge', '*', '', 'name');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$html .= "<select name='" . $varName . "'>\n";
	foreach($range as $row) {
		$html .= "\t<option value='" . $row['UID'] . "'>" . $row['name'] . "</option>\n";
	}
	$html .= "</select>\n";

	return $html;
}

?>
