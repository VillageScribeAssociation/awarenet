<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show revisions to a project
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('projects_project');

	$model = new Projects_Project($req->ref);
	if (false == $model->loaded) { $page->do404(); }
	if (false == $user->authHas('projects', 'projects_revision', 'show')) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/projects/actions/revisions.page.php');
	$page->blockArgs['raUID'] = $model->alias;
	$page->blockArgs['projectUID'] = $UID;
	$page->blockArgs['projectRa'] = $model->alias;
	$page->blockArgs['projectTitle'] = $model->title;
	$page->render();

?>
