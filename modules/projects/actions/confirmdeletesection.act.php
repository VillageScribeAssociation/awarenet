<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a project section
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('uid', $req->args))
		{ $page->do404('Project (UID) not specified.'); }

	if (false == array_key_exists('section', $req->args))
		{ $page->do404('Section (UID) not specified'); }

	$model = new Projects_Project($req->args['uid']);
	if (false == $model->loaded) { $page->do404('Project not found.'); }
	if (false == $user->authHas('projects', 'Projects_Project', 'edit', $model->UID))
		{ $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	make the confirmation block and show section to be deleted
	//----------------------------------------------------------------------------------------------

	if (false == $model->hasEditAuth($user->UID)) { $page->do403(); }	//TODO: replace
	
	$labels = array(
		'UID' => $model->UID, 
		'section' => $req->args['section'],
		'raUID' => $model->alias
	);
	
	$block = $theme->loadBlock('modules/projects/views/confirmdeletesection.block.php');
	$html = $theme->replaceLabels($labels, $block);
	
	$session->msg($html, 'warn');
	$page->do302('projects/editsection/section_' . $req->args['section'] . '/' . $model->alais);
	//TODO: descurity checks on $req->args['section']

?>
