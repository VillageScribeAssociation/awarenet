<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show a project
//--------------------------------------------------------------------------------------------------
//ref: UID or alias of a Projects_Project object [string]

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('projects_project');

	$model = new Projects_Project($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404('Could not load project.'); }

	//----------------------------------------------------------------------------------------------
	//	render page
	//----------------------------------------------------------------------------------------------	
	$kapenta->page->load('modules/projects/actions/show.page.php');
	$kapenta->page->blockArgs['raUID'] = $model->alias;
	$kapenta->page->blockArgs['UID'] = $UID;
	$kapenta->page->blockArgs['projectTitle'] = $model->title;
	$kapenta->page->blockArgs['projectRa'] = $model->alias;
	$kapenta->page->render();

?>
