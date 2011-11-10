<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a project abstract
//--------------------------------------------------------------------------------------------------

	if ('' == $req->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('projects_project');

	//----------------------------------------------------------------------------------------------
	//	check user is authorised to edit this projects abstract
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Project($UID);
	if (false == $model->loaded) { $page->do404('Project not found.'); }

	if (false == $user->authHas('projects', 'projects_project', 'edit', $model->UID)) {
		$page->do403('You are not permitted to edit this project abstract.');
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/projects/actions/editabstract.page.php');
	$page->blockArgs['raUID'] = $model->alias;
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['title'] = $model->title;
	//$page->blockArgs['viewProjectUrl'] = $kapenta->serverPath . 'projects/' . $model->alias;
	$page->render();

?>
