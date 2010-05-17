<?

//--------------------------------------------------------------------------------------------------
//	show a project record
//--------------------------------------------------------------------------------------------------
	
	//----------------------------------------------------------------------------------------------
	//	TODO: check permissions here
	//----------------------------------------------------------------------------------------------
	if ($request['ref'] == '') { do404(); }
	raFindRedirect('projects', 'show', 'projects', $request['ref']);
	require_once($installPath . 'modules/projects/models/project.mod.php');

	//----------------------------------------------------------------------------------------------
	//	load the model and determine if the current user can edit it
	//----------------------------------------------------------------------------------------------
	$model = new Project($request['ref']);
	$members = $model->getMembers();	
	$editUrl = '';

	if ($model->hasEditAuth($user->data['UID']) == true) 
		{ $editUrl = $serverPath . 'projects/editabstract/' . $model->data['recordAlias']; }

	//----------------------------------------------------------------------------------------------
	//	render page
	//----------------------------------------------------------------------------------------------	
	$page->load($installPath . 'modules/projects/actions/show.page.php');
	$page->blockArgs['raUID'] = $request['ref'];
	$page->blockArgs['UID'] = raGetOwner($request['ref'], 'projects');
	$page->blockArgs['projectTitle'] = $model->data['title'];
	$page->blockArgs['projectRa'] = $model->data['recordAlias'];
	$page->blockArgs['editProjectUrl'] = $editUrl;
	$page->render();

?>
