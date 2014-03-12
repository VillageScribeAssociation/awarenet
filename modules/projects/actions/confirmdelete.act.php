<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show a message asking the user if they really want to delete a project
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------		
	if (false == array_key_exists('UID', $kapenta->request->args)) { $kapenta->page->do404(); }

	$model = new Projects_Project($kapenta->request->args['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Project not found.'); }
	if (false == $kapenta->user->authHas('projects', 'projects_project', 'delete', $model->UID))
		{ $kapenta->page->do403('You are not authorized to delete this project.'); }	
	
	//----------------------------------------------------------------------------------------------
	//	make the confirmation block
	//----------------------------------------------------------------------------------------------		
	$labels = array('UID' => $model->UID, 'raUID' => $model->alias);
	$block = $theme->loadBlock('modules/projects/views/confirmdelete.block.php');
	$html = $theme->replaceLabels($labels, $block);
	$kapenta->session->msg($html, 'warn');
	$kapenta->page->do302('projects/' . $model->alias);

?>
