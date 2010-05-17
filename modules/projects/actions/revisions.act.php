<?

//--------------------------------------------------------------------------------------------------
//	show revisions to a project
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	TODO: check permissions here
	//----------------------------------------------------------------------------------------------
	if ($request['ref'] == '') { do404(); }
	$UID = raFindRedirect('projects', 'show', 'projects', $request['ref']);
	require_once($installPath . 'modules/projects/models/project.mod.php');

	//----------------------------------------------------------------------------------------------
	//	load the model
	//----------------------------------------------------------------------------------------------
	$model = new Project($request['ref']);

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page = new Page($installPath . 'modules/projects/actions/revisions.page.php');
	$page->blockArgs['raUID'] = $request['ref'];
	$page->blockArgs['projectUID'] = $UID;
	$page->blockArgs['projectRa'] = $request['ref'];
	$page->blockArgs['projectTitle'] = $model->data['title'];
	$page->render();

?>
