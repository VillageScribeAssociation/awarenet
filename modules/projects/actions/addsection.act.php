<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/section.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/sections.set.php');
	require_once($kapenta->installPath . 'modules/projects/models/changes.set.php');

//--------------------------------------------------------------------------------------------------
//*	add a new section to a project
//--------------------------------------------------------------------------------------------------
//postarg: action - set to addSection [string]
//postarg: projectUID - UID of a Projects_Project object [string]
//postarg: title - title of new section [string]
//postopt: content - content new section [string]

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('action not specified'); }
	if ('addSection' != $_POST['action']) { $page->do404('action not supported'); }
	if (false == array_key_exists('projectUID', $_POST)) { $page->do404('Project UID not given'); }

	$project = new Projects_Project($_POST['projectUID']);
	if (false == $project->loaded) { $page->do404('Project not found.'); }	

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	add the section
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Section();
	foreach($_POST as $key => $value) {
		switch($key) {
			case 'title':		$model->title = $utils->cleanTitle($value); 		break;
			case 'content':		$model->content = $utils->cleanHtml($value);	 	break;
		}
	}

	$sections = new Projects_Sections($project->UID);
	if (false == $sections->loaded) { $session->msg("Could not load project sections."); }

	$model->projectUID = $project->UID;
	$model->weight = $sections->getMaxWeight() + 1;
	$report = $model->save();

	if ('' == $report) {
		$session->msg('Added new section: ' . $model->title, 'ok');

		//------------------------------------------------------------------------------------------
		//	add a revisions to title, content and weights
		//------------------------------------------------------------------------------------------
		$changes = new Projects_Changes($model->projectUID, $model->UID);
		$check = '';

		$msg = "Add new section:";
		$check .= $changes->add('s.new', $msg, $model->UID);

		$msg = "Set section title:";
		$check .= $changes->add('s.title', $msg, $model->title);

		if ('' != trim($model->content)) {
			$msg = "Set section content:";
			$check .= $changes->add('s.content', $msg, $model->content);
		}

		if ('' == $check) { $session->msg('Added revisions.', 'ok'); }
		else { $session->msg('Problem while addingrevisions:<br/>' . $check, 'bad'); }

	} else {
		$session->msg('Could not create new section:<br/>' . $report, 'bad');
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to project page
	//----------------------------------------------------------------------------------------------

	$page->do302('projects/show/' . $project->alias . '#s' . $model->UID);

?>
