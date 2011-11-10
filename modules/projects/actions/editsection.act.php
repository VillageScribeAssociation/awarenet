<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/section.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a project section
//--------------------------------------------------------------------------------------------------
//ref: UID of a Projects_Section object [string]

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404('section not specified'); }
	$model = new Projects_Section($req->ref);
	if (false == $model->loaded) { $page->do404('section not found', true); }

	if (false == $user->authHas('projects', 'projects_project', 'edit', $model->projectUID)) {
		$page->do403('You are not permitted to edit this project.', true);
	}

	//----------------------------------------------------------------------------------------------
	//	check lock and set if not present
	//----------------------------------------------------------------------------------------------

	$lockedBy = $model->checkLock();

	if (('' == $lockedBy) || ($user->UID == $lockedBy)) {
		$check = $model->setLock($user->UID);
		if (false == $check) { $session->msg('Database Error - Could not set lock.'); }

	} else {
		$session->msg('Someone else is editing this section, please wait and try again.');
	}

	//----------------------------------------------------------------------------------------------
	//	load the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/projects/actions/editsection.if.page.php');
	$page->blockArgs['raUID'] = $model->UID;
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['projectUID'] = $model->projectUID;
	$page->blockArgs['sectionUID'] = $model->UID;
	$page->render();

?>
