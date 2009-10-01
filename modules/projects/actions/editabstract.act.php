<?

//--------------------------------------------------------------------------------------------------
//	edit a project abstract
//--------------------------------------------------------------------------------------------------

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
	//	load the page (or 403)
	//----------------------------------------------------------------------------------------------

	if ($authorised == true) {
		$page->load($installPath . 'modules/projects/actions/editabstract.page.php');
		$page->blockArgs['raUID'] = $request['ref'];
		$page->blockArgs['UID'] = $projectUID;
		$page->blockArgs['viewProjectUrl'] = $serverPath . 'projects/' . $request['ref'];
		$page->render();
	} else {
		do403();
	}

?>
