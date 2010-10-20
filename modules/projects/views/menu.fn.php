<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	menu for projects, no arguments
//--------------------------------------------------------------------------------------------------
//opt: raUID - alias or UID of a Projects_Project object [string]
//opt: projectUID - UID of a Projects_Projects object [string]

function projects_menu($args) {
	global $user, $theme;

	$labels = array();
	$labels['newEntry'] = '';				// defaults for public/unauthorized user
	$labels['editProject'] = '';		// ...
	$labels['viewProject'] = '';		// ...
	$labels['viewHistory'] = '';			// ...
	$labels['delProject'] = '';		// ...

	$ts = 'theme::submenu';

	//----------------------------------------------------------------------------------------------
	//	check is projectUID has been provided, is correct and and user has edit permission
	//----------------------------------------------------------------------------------------------

	if (true == array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }

	$editAuth = false;
	if (true == array_key_exists('raUID', $args)) { 
		$model = new Projects_Project($args['raUID']);
		if (true == $model->loaded) {
			//--------------------------------------------------------------------------------------
			//	menu options for specific project, not projects in general
			//--------------------------------------------------------------------------------------

			$viewUrl = '%%serverPath%%projects/show/' . $model->alias;
			$labels['viewProject'] = "[[:$ts::label=View This Project::link=$viewUrl:]]";

			$newUrl = '%%serverPath%%projects/new/';
			$labels['newEntry'] = "[[:$ts::label=Create New Project::link=$newUrl:]]";

			$histUrl = '%%serverPath%%projects/history/' . $model->alias;
			$labels['viewHistory'] = "[[:theme::submenu::label=History::link=$histUrl:]]";

			if ($user->authHas('projects', 'Projects_Project', 'edit', $model->UID)) {
				//----------------------------------------------------------------------------------
				//	user can edit this project
				//----------------------------------------------------------------------------------
				$editUrl = '%%serverPath%%projects/editabstract/' . $model->alias;
				$labels['editProject'] = "[[:$ts::label=Edit This Project::link=" . $editUrl . ":]]";

				// only admins may delete projects
				if ('admin' == $user->role) {
					$delUrl = '%%serverPath%%projects/confirmdelete/UID_' . $model->UID;
					$labels['delProject'] = "[[:$ts::label=Delete This Project::link=$delUrl:]]";;	
				}


			}
		}
	}

	if (true == $editAuth) {

		$labels['editCurrentProject'] = '';
		$labels['viewCurrentProject'] = '';

		if ((array_key_exists('editProjectUrl', $args)) && ($args['editProjectUrl'] != '')) { 

		}

		if (true == array_key_exists('viewProjectUrl', $args)) { 

		}



	} else { $labels['newEntry'] = ''; }
	
	$block = $theme->loadBlock('modules/projects/views/menu.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>

