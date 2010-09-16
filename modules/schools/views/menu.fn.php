<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	menu for schools, no arguments
//--------------------------------------------------------------------------------------------------

function schools_menu($args) {
	global $theme;

	global $user;
	$labels = array();
	if ($user->authHas('schools', 'Schools_School', 'edit', 'TODO:UIDHERE')) {
		$labels['newEntry'] = '[[:theme::submenu::label=Add School::link=/schools/new/:]]';
	} else { $labels['newEntry'] = ''; }
	
	$html = $theme->replaceLabels($labels, $theme->loadBlock('modules/schools/views/menu.block.php'));
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>