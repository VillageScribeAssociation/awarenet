<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list contact details for all schools
//--------------------------------------------------------------------------------------------------

function schools_allschoolcontacts($args) {
	global $theme, $db;
	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	// none yet

	//----------------------------------------------------------------------------------------------
	//	load all (visible) schools by name
	//----------------------------------------------------------------------------------------------
	$conditions = array();			//% table filter [array:string]
	$conditions[] = "hidden='no'";
	// add any other conditions here

	$range = $db->loadRange('schools_school', '*', $conditions, 'name');

	//----------------------------------------------------------------------------------------------
	//	make the display
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/schools/views/allschoolcontacts.block.php');
	foreach($range as $item) {
		$model = new Schools_School();
		$model->loadArray($item);
		$html .= $theme->replaceLabels($model->toArray(), $block);
	}

	return $html;
}

?>
