<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return a list of schools
//--------------------------------------------------------------------------------------------------
//opt: varname - name of variable (default is 'school') [string]
//opt: default - default value (should be the UID of a school record) [string]

function schools_select($args) {
		global $kapenta;
		global $kapenta;

	$varname = 'school';
	$default = '';
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permisisons
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('varname', $args)) { $varname = $args['varname']; }
	if (array_key_exists('default', $args)) { $default = $args['default']; }
	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	check arguments and permisisons
	//----------------------------------------------------------------------------------------------
	$range = $kapenta->db->loadRange('schools_school', '*', '', 'name');
	//$sql = "select * from Schools_School order by name";
	
	$html .= "<select name='" . $varname . "' style='width: 200px'>\n";
	foreach ($range as $row) {
		if ($row['UID'] == $default) {
			$html .= "<option value='" . $row['UID'] . "' selected='YES'>" 
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
