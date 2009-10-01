<?

//--------------------------------------------------------------------------------------------------
//	edit a project section
//--------------------------------------------------------------------------------------------------

	if ($request['ref'] == '') { do404(); }
	if (array_key_exists('section', $request['args']) == false) { do404(); }
	raFindRedirect('projects', 'editabstract', 'projects', $request['ref']);

	$projectUID = raGetOwner($request['ref'], 'projects');
	$sectionUID = $request['args']['section'];

	//----------------------------------------------------------------------------------------------
	//	check user is authorised to edit this project
	//----------------------------------------------------------------------------------------------

	$authorised = false;

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	$pM = new ProjectMembership();
	if (false == $pM->load($projectUID, $user->data['UID'])) {
		$_SESSION['sMessage'] .= "You are not a member of this project, you can't edit it.<br/>\n";
		do302('projects/' . $projectUID);
	}

	if (($pM->data['role'] == 'member') OR ($pM->data['role'] == 'admin')) { $authorised = true; }
	if ($user->data['ofGroup'] == 'admin') { $authorised = true; }

	//----------------------------------------------------------------------------------------------
	//	load the page (or 403)
	//----------------------------------------------------------------------------------------------

	if ($authorised == true) {
		$page->load($installPath . 'modules/projects/actions/editsection.page.php');
		$page->blockArgs['raUID'] = $request['ref'];
		$page->blockArgs['UID'] = $projectUID;
		$page->blockArgs['projectUID'] = $projectUID;
		$page->blockArgs['sectionUID'] = $sectionUID;
		$page->blockArgs['viewProjectUrl'] = $serverPath . 'projects/' . $request['ref'];
		$page->render();
	} else {
		do403();
	}

?>
