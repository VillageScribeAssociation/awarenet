<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');

//-------------------------------------------------------------------------------------------------
//*	show project index with edit links
//-------------------------------------------------------------------------------------------------

	if ('' == $req->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('Projects_Project');

	//----------------------------------------------------------------------------------------------
	//	check user is authorised to edit this projects abstract
	//----------------------------------------------------------------------------------------------

	$model = new Projects_Project($UID);

	if ((false == $model->isMember($user->UID)) && ('admin' != $user->role)) {
		// TODO: use a permission for this
		$session->msg("You are not a member of this project, you can't edit it.", 'bad');
		$page->do302('projects/' . $model->alias);
	}

	//----------------------------------------------------------------------------------------------
	//	increment or decrement section weights (move up or down)
	//----------------------------------------------------------------------------------------------

	if (array_key_exists('inc', $req->args) == true) {
		//$session->msg("incrementing section " . $req->args['inc'], 'ok');
		$model->incrementSection($req->args['inc']);
	}

	if (array_key_exists('dec', $req->args) == true) {
		//$session->msg("decrementing section " . $req->args['inc'], 'ok');
		$model->decrementSection($req->args['dec']);
	}

	//----------------------------------------------------------------------------------------------
	//	load the page (or 403)
	//----------------------------------------------------------------------------------------------

	$page->load('modules/projects/actions/editindex.if.page.php');
	$page->blockArgs['raUID'] = $model->alias;
	$page->blockArgs['UID'] = $model->UID;
	$page->render();

?>
