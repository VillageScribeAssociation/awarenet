<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	menu for projects, no arguments
//--------------------------------------------------------------------------------------------------
//opt: projectUID - UID of a Projects_Projects object [string]

function projects_menu($args) {
	global $user, $theme;

	$labels = array();
	$labels['newEntry'] = '';				// defaults for public/unauthorized user
	$labels['editCurrentProject'] = '';		// ...
	$labels['viewCurrentProject'] = '';		// ...
	$labels['delCurrentProject'] = '';		// ...

	//----------------------------------------------------------------------------------------------
	//	check is projectUID has been provided and user has edit permission
	//----------------------------------------------------------------------------------------------
	$editAuth = false;
	if (true == array_key_exists('projectUID', $args)) { 
		$model = new Projects_Project($args['projectUID']);
		if (true == $model->loaded) {
			if ($user->authHas('projects', 'Projects_Project', 'edit', $model->UID)) {
				$editAuth = true;
			}
		}
	}

	if (true == $editAuth) {
		$labels['newEntry'] = '[[:theme::submenu::label=Create New Project::link=/projects/new/:]]';
		$labels['editCurrentProject'] = '';
		$labels['viewCurrentProject'] = '';

		if ((array_key_exists('editProjectUrl', $args)) && ($args['editProjectUrl'] != '')) { 
			$labels['editCurrentProject'] = "[[:theme::submenu::label=Edit This Project" 
										  . "::link=" . $args['editProjectUrl'] . ":]]";

			// only admins may delete projects
			if ('admin' == $user->role) {
				$labels['delCurrentProject'] = "[[:theme::submenu::label=Delete This Project" 
										  	. "::link=" . $args['delProjectUrl'] . ":]]";;	
			}

		}

		if (true == array_key_exists('viewProjectUrl', $args)) { 
			$labels['viewCurrentProject'] = "[[:theme::submenu::label=View This Project" 
										  . "::link=" . $args['viewProjectUrl'] . ":]]";

		}



	} else { $labels['newEntry'] = ''; }
	
	$block = $theme->loadBlock('modules/projects/views/menu.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>

