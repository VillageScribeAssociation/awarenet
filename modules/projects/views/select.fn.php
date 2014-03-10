<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return an HTML select box of all projects
//--------------------------------------------------------------------------------------------------
//opt: varname - name of variable (default is 'project') [string]
//opt: default - default selected item (set to a project UID) [string]

function projects_select($args) {
	global $kapenta;

	$varname = 'project';
	$default = '';
	if (array_key_exists('varname', $args)) { $varname = $args['varname']; }
	if (array_key_exists('default', $args)) { $default = $args['default']; }
	$html = '';
	
	$sql = "select * from projects_project order by name";
	$result = $kapenta->db->query($sql);
	$html .= "<select name='" . $varname . "'>\n";
	while ($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);
		if ($row['UID'] == $default) {
			$html .= "<option value='" . $row['UID'] . "' CHECKED='CHECKED'>" 
			      . $row['name'] . "</option>\n";
		} else {
			$html .= "<option value='" . $row['UID'] . "'>" . $row['name'] . "</option>\n";
		}
	}
	$html .= "</select>\n";
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>