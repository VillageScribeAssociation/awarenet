<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a project abstract
//--------------------------------------------------------------------------------------------------

	if ('' == $req->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('Projects_Project');

	//----------------------------------------------------------------------------------------------
	//	check user is authorised to edit this projects abstract
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Project($UID);
	if (false == $model->loaded) { $page->do404('Project not found.'); }

	if ((false == $model->isMember($user->UID)) && ('admin' != $user->role)) {
		// TODO: use a permission for this
		$session->msg("You are not a member of this project, you can't edit it.", 'bad');
		$page->do302('projects/' . $model->alias);
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/projects/actions/editabstract.page.php');
	$page->blockArgs['raUID'] = $model->alias;
	$page->blockArgs['UID'] = $model->UID;
	//$page->blockArgs['viewProjectUrl'] = $serverPath . 'projects/' . $model->alias;
	$page->render();

?>
