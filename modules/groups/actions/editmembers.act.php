<?

//--------------------------------------------------------------------------------------------------
//	iframe to display and add group members, reference should be a group's recordalias
//--------------------------------------------------------------------------------------------------

	if ($request['ref'] == '') { do404(); }
	raFindRedirect('groups', 'editmembers', 'groups', $request['ref']);

	require_once($installPath . 'modules/groups/models/groups.mod.php');
	$model = new Group($request['ref']);
	$members = $model->getMembers();

	//----------------------------------------------------------------------------------------------
	//	determine if current user is authorised to administer this group
	//----------------------------------------------------------------------------------------------
	
	$admin = $model->hasEditAuth($user->data['UID']);

	foreach($members as $mbr) {
		if (($mbr['user'] == $user->data['UID']) AND ($mbr['admin'] == 'yes')) { $admin = true; }
	}

	if ($admin == false) { do403(); }

	//----------------------------------------------------------------------------------------------
	//	accept HTTP POSTs to add new members
	//----------------------------------------------------------------------------------------------

	if ( (array_key_exists('action', $_POST) == true)
		AND ($_POST['action'] == 'addMember') ) {

		$model->addMember($_POST['user'], $_POST['position'], $_POST['admin']);
		$_SESSION['sMessage'] .= "Add new member to " . $model->data['name'] . ".<br/>\n";

	}

	//----------------------------------------------------------------------------------------------
	//	accept HTTP POSTs to remove members
	//----------------------------------------------------------------------------------------------

	if ( (array_key_exists('action', $_POST) == true)
		AND ($_POST['action'] == 'removeMember') ) {

		$model->removeMember($_POST['user']);
		$_SESSION['sMessage'] .= "Removed member from " . $model->data['name'] . ".<br/>\n";

	}

	//----------------------------------------------------------------------------------------------
	//	accept HTTP GET to remove members (user UID is given)
	//----------------------------------------------------------------------------------------------
	// eg /groups/editmembers/removemember_8237146489/Some-group

	if ( (true == array_key_exists('removemember', $request['args']))
		AND (true == dbRecordExists('users', $request['args']['removemember'])) ) {
		$model->removeMember($request['args']['removemember']);
		$_SESSION['sMessage'] .= "Removed member from " . $model->data['name'] . ".<br/>\n";

	}
	
	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	
	$page->load($installPath . 'modules/groups/actions/editmembers.if.page.php');
	$page->blockArgs['UID'] = $model->data['UID'];
	$page->blockArgs['raUID'] = $model->data['recordAlias'];
	$page->render();

?>
