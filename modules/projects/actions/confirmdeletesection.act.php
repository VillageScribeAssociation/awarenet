<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a project section
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('UID', $kapenta->request->args))
		{ $kapenta->page->do404('Project (UID) not specified.'); }

	if (false == array_key_exists('section', $kapenta->request->args))
		{ $kapenta->page->do404('Section (UID) not specified'); }

	$project = new Projects_Project($kapenta->request->args['UID']);
	if (false == $project->loaded) { $kapenta->page->do404('Project not found.'); }
	if (false == $user->authHas('projects', 'projects_project', 'edit', $project->UID)) {
		$kapenta->page->do403("You are not authorized to edit this project.");
	}

	$model = new Projects_Section($kapenta->request->args['section']);
	if (false == $model->loaded) { $kapenta->page->do404("Section not found."); }

	//----------------------------------------------------------------------------------------------
	//	make the confirmation block and show section to be deleted
	//----------------------------------------------------------------------------------------------

	$labels = array(
		'project' => $project->UID, 
		'projectRa' => $project->alias,
		'UID' => $model->UID,
		'section' => $model->UID,
		'raUID' => $model->UID
	);
	
	$block = $theme->loadBlock('modules/projects/views/confirmdeletesection.block.php');
	$html = $theme->replaceLabels($labels, $block);
	
	$session->msg($html, 'warn');
	$kapenta->page->do302('projects/editsection/' . $model->UID);
	//TODO: descurity checks on $kapenta->request->args['section']

?>
