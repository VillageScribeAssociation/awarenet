<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display complete revision history for a given project
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('projects_project');
	$model = new Projects_Project($UID);
	if (false == $model->loaded) { $page->do404('No such project.'); }

	$pageNo = 1;
	if (true == array_key_exists('page', $req->args)) { $pageNo = (int)$req->args['page']; }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/projects/actions/history.page.php');
	$page->blockArgs['UID'] = $UID;
	$page->blockArgs['raUID'] = $req->ref;
	$page->blockArgs['articleTitle'] = $model->title;
	$page->blockArgs['pageNo'] = $pageNo . '';
	$page->render();

?>
