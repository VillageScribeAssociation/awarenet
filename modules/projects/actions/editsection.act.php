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
	if ('' == $kapenta->request->ref) { $page->do404('section not specified'); }
	$model = new Projects_Section($kapenta->request->ref);
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
	$kapenta->page->load('modules/projects/actions/editsection.if.page.php');
	$kapenta->page->blockArgs['raUID'] = $model->UID;
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['projectUID'] = $model->projectUID;
	$kapenta->page->blockArgs['sectionUID'] = $model->UID;
	$kapenta->page->render();

?>
