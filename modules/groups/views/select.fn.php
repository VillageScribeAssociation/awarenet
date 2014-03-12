<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return a list of groups
//--------------------------------------------------------------------------------------------------
//opt: varname - name of variable (default is 'group') [string]
//opt: default - set default value (should be a group UID) [string]

function groups_select($args) {
		global $kapenta;
		global $kapenta;

	$varname = 'group';
	$default = '';
	$html = '';
	
	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('varname', $args)) { $varname = $args['varname']; }
	if (true == array_key_exists('default', $args)) { $default = $args['default']; }
	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	make and return the select element
	//----------------------------------------------------------------------------------------------
	$range = $kapenta->db->loadRange('groups_group', '*', '', 'name');
	//$sql = "select * from Groups_Group order by name";

	$html .= "<select name='" . $varname . "'>\n";
	foreach ($range as $row) {
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
