<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to a project abstract
//--------------------------------------------------------------------------------------------------
//postarg: action - must be 'saveAbstract' [string]
//postarg: UID - UID of a Projects_Project object [string]
//postarg: abstract - html [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST vars
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('action not specified'); }
	if ('saveAbstract' != $_POST['action']) { $page->do404('action not supported'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not specified'); }

	$model = new Projects_Project($_POST['UID']);
	if (false == $model->loaded) { $page->do404('Project not found.'); }

	if (false == $user->authHas('projects', 'projects_project', 'edit', $model->UID)) {
		$page->do403('You are not authorized to edit this project.'); 
	}

	if (false == array_key_exists('abstract', $_POST)) { $page->do404('abstract not given'); }

	//----------------------------------------------------------------------------------------------
	//	note the revision
	//----------------------------------------------------------------------------------------------
	if ($model->abstract != $_POST['abstract']) { 
		$report = $model->saveRevision();
		if ('' == $report) { $session->msg('Saved revision.', 'ok'); }
		else { $session->msg('Revision not saved.', 'bad'); }
	}

	//----------------------------------------------------------------------------------------------
	//	save the record
	//----------------------------------------------------------------------------------------------
	$model->abstract = $utils->cleanHtml($_POST['abstract']);
	$report = $model->save();
	if ('' == $report) { 
		//------------------------------------------------------------------------------------------
		//	raise 'project_saved' event
		//------------------------------------------------------------------------------------------
		$args = array(
			'UID' => $model->UID,
			'user' => $user->UID,
			'section' => 'abstract'
		);

		$kapenta->raiseEvent('projects', 'project_saved', $args);
		$session->msg('Saved changes to abstract.', 'ok'); 
	} else {
		$session->msg('Could not save changes to abstract.', 'bad');
	}		
		
	$page->do302('projects/edit/' . $model->alias);

?>

