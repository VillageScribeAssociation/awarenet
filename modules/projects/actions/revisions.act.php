<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show revisions to a project
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('projects_project');

	$model = new Projects_Project($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404(); }
	if (false == $user->authHas('projects', 'projects_revision', 'show')) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/projects/actions/revisions.page.php');
	$kapenta->page->blockArgs['raUID'] = $model->alias;
	$kapenta->page->blockArgs['projectUID'] = $UID;
	$kapenta->page->blockArgs['projectRa'] = $model->alias;
	$kapenta->page->blockArgs['projectTitle'] = $model->title;
	$kapenta->page->render();

?>
