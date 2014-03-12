<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/section.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/change.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/changes.set.php');

//--------------------------------------------------------------------------------------------------
//*	revert some part of a project to a specifed revision
//--------------------------------------------------------------------------------------------------
//ref: should be set the UID of a Projects_Change object [string]

	//----------------------------------------------------------------------------------------------
	//	check reference and permission
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }
	
	$model = new Projects_Change($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404('Revision not found.'); }

	$project = new Projects_project($model->projectUID);
	if (false == $project->loaded) { $kapenta->page->do404('Project not found.'); }

	if ('open' != $project->status) { $kapenta->page->do403($project->status); }

	$section = new Projects_Section();
	if (('' != $model->sectionUID) && ('*' != $model->sectionUID)) {
		$section->load($model->sectionUID);
		if (false == $section->loaded) { $kapenta->page->do404('Section not found.'); }
	}

	$changes = new Projects_Changes($model->projectUID, $model->sectionUID);

	if (false == $kapenta->user->authHas('projects', 'projects_project', 'edit', $model->projectUID)) {
		$kapenta->page->do403('You are not permitted to edit this project.', true);
	}

	//----------------------------------------------------------------------------------------------
	//	make the revision
	//----------------------------------------------------------------------------------------------
	//TODO: more checks here
	//TODO: figure out undeletion, should probably have its own icon

	switch($model->changed) {
		case 's.title':
			if ($section->title != $model->value) {
				$section->title = $model->value;
				$section->save();
				$changes->add('s.title', 'Changed section title:', $model->value);
				$kapenta->session->msg('Reverted section title to: ' . $model->value, 'ok');
			}
			break;	//..............................................................................

		case 's.content':
			if ($section->content != $model->value) {
				$section->content = $model->value;
				$section->save();
				$changes->add('s.content', 'Changed section content:', $model->value);
				$kapenta->session->msg('Reverted section content.', 'ok');
			}
			break;	//..............................................................................

		case 'p.title':
			if ($project->title != $model->value) {
				$project->title = $model->value;
				$project->save();
				$changes->add('p.title', 'Changed project title:', $model->value);
				$kapenta->session->msg('Reverted project title to: ' . $model->value, 'ok');
			}
			break;	//..............................................................................

		case 'p.abstract':
			if ($project->abstract != $model->value) {
				$project->abstract = $model->value;
				$project->save();
				$changes->add('p.abstract', 'Changed project abstract:', $model->value);
				$kapenta->session->msg('Reverted project abstract.', 'ok');
			}
			break;	//..............................................................................

		default:
			$kapenta->session->msg('Cannot revert to: ' . $model->changed, 'bad');
			break;	//..............................................................................

	}	

	//----------------------------------------------------------------------------------------------
	//	redirect back to project
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302('projects/' . $model->projectUID);

?>
