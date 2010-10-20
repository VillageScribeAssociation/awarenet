<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a project section
//--------------------------------------------------------------------------------------------------

	if ('' == $req->ref) { $page->do404('no project specified'); }
	if (false == array_key_exists('section', $req->args)) { $page->do404('section not given'); }
	$UID = $aliases->findRedirect('Projects_Project');
	$sectionUID = $req->args['section'];

	//----------------------------------------------------------------------------------------------
	//	check user is authorised to edit this project
	//----------------------------------------------------------------------------------------------

	$model = new Projects_Project($UID);
	if (false == $model->loaded) { $page->do404('no such project'); }

	if ((false == $model->isMember($user->UID)) && ('admin' != $user->role)) {
		// TODO: use a permission for this
		$session->msg("You are not a member of this project, you can't edit it.", 'bad');
		$page->do302('projects/' . $model->alias);
	}

	//----------------------------------------------------------------------------------------------
	//	load the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/projects/actions/editsection.page.php');
	$page->blockArgs['raUID'] = $model->alias;
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['projectUID'] = $model->UID;
	$page->blockArgs['sectionUID'] = $sectionUID;
//	$page->blockArgs['viewProjectUrl'] = $serverPath . 'projects/' . $model->alias;
	$page->render();

?>
