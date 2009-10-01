<?

//--------------------------------------------------------------------------------------------------
//	iframe to display and add/remove project members, reference should be a projects' recordalias
//--------------------------------------------------------------------------------------------------

	if ($request['ref'] == '') { do404(); }
	raFindRedirect('projects', 'editmembers', 'projects', $request['ref']);

	require_once($installPath . 'modules/projects/models/projects.mod.php');
	$model = new Project($request['ref']);
	$members = $model->getMembers();

	//----------------------------------------------------------------------------------------------
	//	determine if current user is authorised to administer this project
	//----------------------------------------------------------------------------------------------
	
	$admin = false;
	if ($user->data['ofGroup'] == 'admin') { $admin = true; }

	foreach($members as $userUID => $role) {
		if (($userUID == $user->data['UID']) AND ($role == 'admin')) { $admin = true; }
	}

	if ($admin == false) { 
		echo "You cannot administer memberships of this project.<br/>\n";
		flush();
		die();
	}

	//----------------------------------------------------------------------------------------------
	//	accept HTTP POSTs to add new members
	//----------------------------------------------------------------------------------------------

	if ( (array_key_exists('action', $_POST) == true)
		AND ($_POST['action'] == 'addMember') ) {

		$model->addMember($_POST['user'], $_POST['role']);
		$_SESSION['sMessage'] .= "Added new member to project: "
							   . $model->data['title'] . ".<br/>\n";

	}

	//----------------------------------------------------------------------------------------------
	//	accept HTTP GET to remove members (user UID is given)
	//----------------------------------------------------------------------------------------------
	// eg /projects/editmembers/removemember_8237146489/Some-Project

	if ( (true == array_key_exists('removemember', $request['args']))
		AND (true == dbRecordExists('users', $request['args']['removemember'])) ) {
		$model->removeMember($request['args']['removemember']);
		$_SESSION['sMessage'] .= "Removed member from " . $model->data['title'] . ".<br/>\n";

	}
	
	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	
	$page->load($installPath . 'modules/projects/actions/editmembers.if.page.php');
	$page->blockArgs['UID'] = $model->data['UID'];
	$page->blockArgs['raUID'] = $model->data['recordAlias'];
	$page->render();

?>
