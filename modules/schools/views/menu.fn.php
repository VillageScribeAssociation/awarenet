<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	menu for schools, no arguments
//--------------------------------------------------------------------------------------------------

function schools_menu($args) {
		global $theme;
		global $user;


	$labels = array('newEntry' => '', 'allContactDetails' => '');

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	if (true == $user->authHas('schools', 'schools_school', 'new')) {
		$labels['newEntry'] = '[[:theme::submenu::label=Add School::link=/schools/new/:]]';
	}

	if (('admin' == $user->role) || ('teacher' == $user->role)) {
		$labels['allContactDetails'] = ''
			 . '[[:theme::submenu::label=All Contact Details'
			 . '::link=/schools/schoolcontacts/:]]';
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------	
	$block = $theme->loadBlock('modules/schools/views/menu.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>
