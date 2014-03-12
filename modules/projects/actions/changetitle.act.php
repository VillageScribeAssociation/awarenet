<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/changes.set.php');

//--------------------------------------------------------------------------------------------------
//*	change a project's title
//--------------------------------------------------------------------------------------------------
//postarg: action - must be 'saveChangeTitle' [string]
//postarg: UID - UID of a Projects_Project object [string]
//postarg: title - new title of project [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST vars
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('action not specified'); }
	if ('saveChangeTitle' != $_POST['action']) { $kapenta->page->do404('action not supported'); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('UID not specified'); }

	$model = new Projects_Project($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Project not found.'); }

	if (false == $kapenta->user->authHas('projects', 'projects_project', 'edit', $model->UID)) {
		$kapenta->page->do403('You are not authorized to edit this project.'); 
	}

	if (false == array_key_exists('title', $_POST)) { $kapenta->page->do404('title not given'); }

	//----------------------------------------------------------------------------------------------
	//	save the record
	//----------------------------------------------------------------------------------------------
	$previous = $model->title;
	$model->title = $utils->cleanString($_POST['title']);		//TODO: better sanitization
	$report = $model->save();
	if ('' == $report) { 
		//------------------------------------------------------------------------------------------
		//	note the revision
		//------------------------------------------------------------------------------------------
		$changes = new Projects_Changes($model->UID, '*');
		if ($model->title != $previous) { 
			$msg = "Changed project title to:";
			$report = $changes->add('p.title', $msg, $model->title);
			if ('' == $report) { $kapenta->session->msg('Saved revision.', 'ok'); }
			else { $kapenta->session->msg('Revision not saved:<br/>' . $report, 'bad'); }
		}


		//------------------------------------------------------------------------------------------
		//	raise 'project_saved' event
		//------------------------------------------------------------------------------------------
		$args = array(
			'UID' => $model->UID,
			'user' => $kapenta->user->UID,
			'section' => 'title'
		);

		$kapenta->raiseEvent('projects', 'project_saved', $args);
		$kapenta->session->msg('Changed project title to: ' . $model->title, 'ok'); 
	} else {
		$kapenta->session->msg('Could not change title.', 'bad');
	}		
		
	$kapenta->page->do302('projects/edit/' . $model->alias);

?>
