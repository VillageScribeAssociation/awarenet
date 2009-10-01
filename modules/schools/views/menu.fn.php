<?

	require_once($installPath . 'modules/schools/models/schools.mod.php');

//--------------------------------------------------------------------------------------------------
//	menu for schools, no arguments
//--------------------------------------------------------------------------------------------------

function schools_menu($args) {
	$labels = array();
	if (authHas('schools', 'edit', '')) {
		$labels['newEntry'] = '[[:theme::submenu::label=Add School::link=/schools/new/:]]';
	} else { $labels['newEntry'] = ''; }
	
	$html = replaceLabels($labels, loadBlock('modules/schools/views/menu.block.php'));
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>