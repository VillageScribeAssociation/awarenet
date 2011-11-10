<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show a project
//--------------------------------------------------------------------------------------------------
//ref: UID or alias of a Projects_Project object [string]

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('projects_project');

	$model = new Projects_Project($req->ref);
	if (false == $model->loaded) { $page->do404('Could not load project.'); }

	//----------------------------------------------------------------------------------------------
	//	render page
	//----------------------------------------------------------------------------------------------	
	$page->load('modules/projects/actions/show.page.php');
	$page->blockArgs['raUID'] = $model->alias;
	$page->blockArgs['UID'] = $UID;
	$page->blockArgs['projectTitle'] = $model->title;
	$page->blockArgs['projectRa'] = $model->alias;
	$page->render();

?>
