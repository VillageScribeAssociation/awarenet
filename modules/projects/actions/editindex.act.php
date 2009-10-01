<?

//-------------------------------------------------------------------------------------------------
//	show project index /w edit links
//-------------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/projects/models/projects.mod.php');

	if ($request['ref'] == '') { do404(); }
	raFindRedirect('projects', 'editabstract', 'projects', $request['ref']);
	$projectUID = raGetOwner($request['ref'], 'projects');

	//----------------------------------------------------------------------------------------------
	//	check user is authorised to edit this projects abstract
	//----------------------------------------------------------------------------------------------

	$authorised = false;

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	$pM = new ProjectMembership();
	if (false == $pM->load($projectUID, $user->data['UID'])) {
		$_SESSION['sMessage'] .= "You are not a member of this project, you can't edit it.<br/>\n";
		$_SESSION['sMessage'] .= "project UID: $projectUID userUID: " . $user->data['UID'] . ".<br/>\n";
		do302('projects/' . $projectUID);
	}

	if (($pM->data['role'] == 'member') OR ($pM->data['role'] == 'admin')) { $authorised = true; }
	if ($user->data['ofGroup'] == 'admin') { $authorised = true; }

	//----------------------------------------------------------------------------------------------
	//	increment or decrement section weights (move up or down)
	//----------------------------------------------------------------------------------------------

	if (array_key_exists('inc', $request['args']) == true) {
		//$_SESSION['sMessage'] .= "incrementing section " . $request['args']['inc'] . "<br/>\n";
		$model = new Project($projectUID);
		$model->incrementSection($request['args']['inc']);
	}

	if (array_key_exists('dec', $request['args']) == true) {
		//$_SESSION['sMessage'] .= "decrementing section " . $request['args']['dec'] . "<br/>\n";
		$model = new Project($projectUID);
		$model->decrementSection($request['args']['dec']);
	}

	//----------------------------------------------------------------------------------------------
	//	load the page (or 403)
	//----------------------------------------------------------------------------------------------

	if ($authorised == true) {
		$page->load($installPath . 'modules/projects/actions/editindex.if.page.php');
		$page->blockArgs['raUID'] = $request['ref'];
		$page->blockArgs['UID'] = $projectUID;
		$page->render();
	} else {
		do403();
	}

?>
