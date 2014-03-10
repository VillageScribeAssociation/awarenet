<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/section.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/changes.set.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to a project section
//--------------------------------------------------------------------------------------------------
//postarg: action - must be 'saveSection' [string]
//postarg: UID - UID of project section [string]
//postarg: title - name of project section [string]
//postarg: content - html [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST vars
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->doXmlError('UID not specified'); }

	$model = new Projects_Section($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->doXmlError('Project section not found.'); }

	$project = new Projects_Project($model->projectUID);
	if (false == $project->loaded) { $kapenta->page->doXmlError('Project not found.'); }

	if (false == $user->authHas('projects', 'projects_section', 'edit', $model->UID)) {
		$kapenta->page->doXmlError('You are not authorized to edit this section.'); 
	}

	//----------------------------------------------------------------------------------------------
	//	check lock
	//----------------------------------------------------------------------------------------------
	//if ($user->UID != $model->checkLock()) {
	//	$kapenta->page->do403('Could not save, you do not own the lock on this section.');
	//}

	//----------------------------------------------------------------------------------------------
	//	save changes to section and clear lock
	//----------------------------------------------------------------------------------------------
	$compare = $model->toArray();

	foreach($_POST as $key => $value) {
		switch($key) {
			case 'title':	$model->title = $utils->cleanTitle($value);		break;
			case 'content':	$model->content = $utils->cleanHtml($value);	break;
			case 'weight':	$model->weight = $value;						break;
		}
	}

	$model->lockedBy = '';	
	$report = $model->save();

	if ('' == $report) {
		//$session->msg('Updated project section: ' . $model->title, 'ok');
	} else {
		$kapenta->page->doXmlError('Could not save section:<br/>' . $report, 'bad');
	}

	//----------------------------------------------------------------------------------------------
	//	save revision (if changed)
	//----------------------------------------------------------------------------------------------
	$changes = new Projects_Changes($model->projectUID, $model->UID);

	if ($model->title != $compare['title']) {
		$msg = 'Changed section title to:';
		$report = $changes->add('s.title', $msg, $model->title);
		if ('' == $report) { $session->msg('Added revision.', 'ok'); }
		else { $session->msg('Could not save revision:<br/>' . $report, 'bad'); }
	}

	if ($model->content != $compare['content']) {
		$msg = 'Changed section content to:';
		$report = $changes->add('s.content', $msg, $model->content);
		if ('' == $report) { $session->msg('Added revision.', 'ok'); }
		else { $session->msg('Could not save revision:<br/>' . $report, 'bad'); }
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to project page
	//----------------------------------------------------------------------------------------------
	//$kapenta->page->do302('projects/show/' . $project->alias . '#s' . $model->UID);

	echo "<ok/>";
	die();

?>
