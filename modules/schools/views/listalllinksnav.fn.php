<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all formatted for nav (300 px wide)
//--------------------------------------------------------------------------------------------------
//arg: hidden - show hidden schools (yes|no) [string]

function schools_listalllinksnav($args) {
		global $kapenta;
		global $kapenta;

	$html = '';				//%	return value [string]
	$showHidden = false;	//%	display hidden schools if true [bool]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	//TODO: add permission check, public for now

	if ((true == array_key_exists('hidden', $args)) && ('yes' == $args['hidden'])) { 
		$showHidden = true; 
	}

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array("(hidden='no' OR hidden='')");
	if (true == $showHidden) { $conditions = ''; }

	$range = $kapenta->db->loadRange('schools_school', '*', $conditions, 'name');

	//----------------------------------------------------------------------------------------------
	//	make the list
	//----------------------------------------------------------------------------------------------
	foreach($range as $row) {
		$viewUrl = '%%serverPath%%schools/show/' . $row['alias'];
		$label = $row['name'] . ' (' . $row['country'] . ')';
		if (false == strpos(strtolower($row['type']), 'school')) {
			$label .= ' (' . $row['type'] . ')';
		}
		$html .= "<a href='$viewUrl'>" . $label . "</a><br/>\n";
	}

	return $html;
}


//--------------------------------------------------------------------------------------------------

?>
