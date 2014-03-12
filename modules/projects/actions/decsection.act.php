<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/section.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/sections.set.php');

//--------------------------------------------------------------------------------------------------
//*	decrement a section's weight
//--------------------------------------------------------------------------------------------------
//postarg: action - set to 'decrementSection'
//postarg: UID - UID of a Projects_Section object [string]

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('action not given'); }
	if ('decrementSection' != $_POST['action']) { $kapenta->page->do404('action not recognized'); }

	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404("UID not given."); }

	$model = new Projects_Section($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404("Section not found."); }

	$project = new Projects_Project($model->projectUID);
	if (false == $project->loaded) { $kapenta->page->do404('Project not found'); }

	if (false == $kapenta->user->authHas('projects', 'projects_project', 'edit', $project->UID)) {
		$kapenta->page->do403();
	}

	//----------------------------------------------------------------------------------------------
	//	increment the section
	//----------------------------------------------------------------------------------------------
	$check = $project->sections->decWeight($model->UID);
	if (false == $check) { $kapenta->page->do404("Could not increment weight."); }

	//----------------------------------------------------------------------------------------------
	//	redirect back to project
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302('projects/' . $project->alias . '#s' . $model->UID);

?>
