<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	menu for schools, no arguments
//--------------------------------------------------------------------------------------------------

function schools_menu($args) {
	global $theme, $user;

	$labels = array('newEntry' => '', 'allContactDetails' => '');

	if ($user->authHas('schools', 'schools_school', 'edit', 'TODO:UIDHERE')) {
		$labels['newEntry'] = '[[:theme::submenu::label=Add School::link=/schools/new/:]]';
	}

	if (('admin' == $user->role) || ('teacher' == $user->role)) {
		$labels['allContactDetails'] = ''
			 . '[[:theme::submenu::label=All Contact Details'
			 . '::link=/schools/schoolcontacts/:]]';
	}
	
	$html = $theme->replaceLabels($labels, $theme->loadBlock('modules/schools/views/menu.block.php'));
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>
