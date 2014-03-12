<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a project abstract
//--------------------------------------------------------------------------------------------------

	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }
	$UID = $aliases->findRedirect('projects_project');

	//----------------------------------------------------------------------------------------------
	//	check user is authorised to edit this projects abstract
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Project($UID);
	if (false == $model->loaded) { $kapenta->page->do404('Project not found.'); }

	if (false == $kapenta->user->authHas('projects', 'projects_project', 'edit', $model->UID)) {
		$kapenta->page->do403('You are not permitted to edit this project abstract.');
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/projects/actions/editabstract.page.php');
	$kapenta->page->blockArgs['raUID'] = $model->alias;
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['title'] = $model->title;
	//$kapenta->page->blockArgs['viewProjectUrl'] = $kapenta->serverPath . 'projects/' . $model->alias;
	$kapenta->page->render();

?>
