<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	menu for projects, no arguments
//--------------------------------------------------------------------------------------------------

function projects_menu($args) {
	$labels = array();
	if (authHas('projects', 'edit', '')) {
		$labels['newEntry'] = '[[:theme::submenu::label=Create New Project::link=/projects/new/:]]';
		$labels['editCurrentProject'] = '';
		$labels['viewCurrentProject'] = '';

		if ((array_key_exists('editProjectUrl', $args)) && ($args['editProjectUrl'] != '')) { 
				$labels['editCurrentProject'] = "[[:theme::submenu::label=Edit This Project" 
											  . "::link=" . $args['editProjectUrl'] . ":]]";
		}

		if (true == array_key_exists('viewProjectUrl', $args)) { 
				$labels['viewCurrentProject'] = "[[:theme::submenu::label=View This Project" 
											  . "::link=" . $args['viewProjectUrl'] . ":]]";
		}

	} else { $labels['newEntry'] = ''; }
	
	$html = replaceLabels($labels, loadBlock('modules/projects/views/menu.block.php'));
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>

