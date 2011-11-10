<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a project section
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('UID', $req->args))
		{ $page->do404('Project (UID) not specified.'); }

	if (false == array_key_exists('section', $req->args))
		{ $page->do404('Section (UID) not specified'); }

	$project = new Projects_Project($req->args['UID']);
	if (false == $project->loaded) { $page->do404('Project not found.'); }
	if (false == $user->authHas('projects', 'projects_project', 'edit', $project->UID)) {
		$page->do403("You are not authorized to edit this project.");
	}

	$model = new Projects_Section($req->args['section']);
	if (false == $model->loaded) { $page->do404("Section not found."); }

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
	$page->do302('projects/editsection/' . $model->UID);
	//TODO: descurity checks on $req->args['section']

?>
