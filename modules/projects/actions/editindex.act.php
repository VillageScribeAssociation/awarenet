<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');

//-------------------------------------------------------------------------------------------------
//*	show project index with edit links
//-------------------------------------------------------------------------------------------------

	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }
	$UID = $aliases->findRedirect('projects_project');

	//----------------------------------------------------------------------------------------------
	//	check user is authorised to edit this projects abstract
	//----------------------------------------------------------------------------------------------

	$model = new Projects_Project($UID);

	if ((false == $model->hasMember($user->UID)) && ('admin' != $user->role)) {
		// TODO: use a permission for this
		$session->msg("You are not a member of this project, you can't edit it.", 'bad');
		$kapenta->page->do302('projects/' . $model->alias);
	}

	//----------------------------------------------------------------------------------------------
	//	increment or decrement section weights (move up or down)
	//----------------------------------------------------------------------------------------------

	if (array_key_exists('inc', $kapenta->request->args) == true) {
		//$session->msg("incrementing section " . $kapenta->request->args['inc'], 'ok');
		$model->incrementSection($kapenta->request->args['inc']);
	}

	if (array_key_exists('dec', $kapenta->request->args) == true) {
		//$session->msg("decrementing section " . $kapenta->request->args['inc'], 'ok');
		$model->decrementSection($kapenta->request->args['dec']);
	}

	//----------------------------------------------------------------------------------------------
	//	load the page (or 403)
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/projects/actions/editindex.if.page.php');
	$kapenta->page->blockArgs['raUID'] = $model->alias;
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->render();

?>
