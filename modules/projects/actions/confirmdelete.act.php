<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//	confirm deletion of a project
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------		
	if (false == array_key_exists('UID', $req->args)) { $page->do404(); }

	$model = new Projects_Project($req->args['UID']);
	if (false == $model->loaded) { $page->do404('Project not found.'); }
	if (false == $user->authHas('projects', 'Projects_Project', 'delete', $model->UID))
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
