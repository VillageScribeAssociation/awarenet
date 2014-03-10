<?

//-------------------------------------------------------------------------------------------------
//*	make an HTML select box for choosing a forum
//-------------------------------------------------------------------------------------------------
//opt: varname - variabnle name, default is 'forum' [string]
//opt: default - UID of a forum for default item [string]

function forums_selectforum($args) {
	global $kapenta;

	$html = '';				//%	return value [string]
	$default = '';
	$varname = 'forum';

	//---------------------------------------------------------------------------------------------
	//	check variables and auth
	//---------------------------------------------------------------------------------------------
	if (true == array_key_exists('varname', $args)) { $varname = $args['varname']; }
	if (true == array_key_exists('default', $args)) { $default = $args['default']; }
	//TODO: permissions check here

	//---------------------------------------------------------------------------------------------
	//	load all forums into a select box
	//---------------------------------------------------------------------------------------------
	$range = $kapenta->db->loadRange('forums_board', '*', '', 'title', '', '');

	$html = "<select name='" . $varname . "'>\n";
	foreach($range as $row) {
		$sel = '';
		if ($row['UID'] == $default) { $sel = "selected='selected'"; } // nb, XHTML
		$html .= "\t<option value='" . $row['UID'] . "' $sel>" . $row['title'] . "</option>\n";
	}
	$html .= "</select>\n";

	return $html;
}

?>
