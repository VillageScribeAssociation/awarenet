<?

	require_once('modules/projects/models/project.mod.php');
	require_once('modules/projects/models/section.mod.php');

//--------------------------------------------------------------------------------------------------
//*	convert all sections attached to a project into equivalent standalone objects
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	convert all sections from all projects
	//----------------------------------------------------------------------------------------------
	$range = $db->loadRange('projects_project', '*', '');
	foreach($range as $item) {
		$project = new Projects_Project($item['UID']);

		$session->msg('Exporting sections from project:' . $project->title);

		foreach($project->sections as $section) {
			$model = new Projects_Section();
			$model->UID = $section['UID'];
			$model->parent = 'root';
			$model->projectUID = $project->UID;
			$model->title = $section['title'];
			$model->content = $section['content'];
			$model->weight = $section['weight'];
			$report = $model->save();

			if ('' == $report) {
				$msg = 'Migrated section: ' . $section['title'] . ' (' . $section['UID'] . ')';
				$session->msg($msg, 'ok');			
			} else {
				$msg = 'Could not migrate: ' . $section['title'] . ' (' . $section['UID'] . ')';
				$session->msg($msg, 'bad');			
			}
		}

	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to projects listing
	//----------------------------------------------------------------------------------------------
	$page->do302('projects/');

?>
