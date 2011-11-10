<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show a message asking the user if they really want to delete a project
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------		
	if (false == array_key_exists('UID', $req->args)) { $page->do404(); }

	$model = new Projects_Project($req->args['UID']);
	if (false == $model->loaded) { $page->do404('Project not found.'); }
	if (false == $user->authHas('projects', 'projects_project', 'delete', $model->UID))
		{ $page->do403('You are not authorized to delete this project.'); }	
	
	//----------------------------------------------------------------------------------------------
	//	make the confirmation block
	//----------------------------------------------------------------------------------------------		
	$labels = array('UID' => $model->UID, 'raUID' => $model->alias);
	$block = $theme->loadBlock('modules/projects/views/confirmdelete.block.php');
	$html = $theme->replaceLabels($labels, $block);
	$session->msg($html, 'warn');
	$page->do302('projects/' . $model->alias);

?>
