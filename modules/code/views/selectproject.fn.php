<?

//--------------------------------------------------------------------------------------------------
//	select box for choosing a user // TODO: security consideration of access to this
//--------------------------------------------------------------------------------------------------
// * $args['default'] = default value (UID of a project)
// * $args['varname'] = field name, default is 'user'

function code_selectproject($args) {
	global $kapenta;

	$varname = 'project';
	$default = '';
	if (array_key_exists('varname', $args) == true) { $varname = $args['varname']; }
	if (array_key_exists('default', $args) == true) { $default = $args['default']; }

	$html = "<select name='" . $varname . "'>\n";
	$sql = "select UID, title from codeprojects order by title";

	$result = $kapenta->db->query($sql);
	while ($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);
		$checked = '';
		if ($row['UID'] == $default) { $checked = "checked='checked'"; }
		$html .= "\t<option value='" . $row['UID'] ."' $checked>". $row['title'] ."</option>";
	}

	$html .= "</select>";
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>