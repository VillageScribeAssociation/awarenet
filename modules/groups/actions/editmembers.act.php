<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');

//--------------------------------------------------------------------------------------------------
//*	iframe to display and add group members, reference should be a group's recordalias
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------

	if ('' == $req->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('groups_group');

	$model = new Groups_Group($UID);
	if (false == $model->loaded) { $page->do404('no such group'); }

	//----------------------------------------------------------------------------------------------
	//	determine if current user is authorised to administer this group
	//----------------------------------------------------------------------------------------------
	$admin = $model->hasEditAuth($user->UID);		//TODO: use a permission for this
	$members = $model->getMembers();
	foreach($members as $member) { 
		if (($member['userUID'] == $user->UID) AND ('yes' == $member['admin'])) { $admin = true; } 
	}

	if ($admin == false) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	accept HTTP POSTs to add new members
	//----------------------------------------------------------------------------------------------
	if ( (array_key_exists('action', $_POST) == true)
		AND ($_POST['action'] == 'addMember') ) {

		$model->addMember($_POST['user'], $_POST['position'], $_POST['admin']);
		$session->msg("Added new member to " . $model->name . ".", 'ok');
	}

	//----------------------------------------------------------------------------------------------
	//	accept HTTP POSTs to remove members
	//----------------------------------------------------------------------------------------------
	if ( (true == array_key_exists('action', $_POST)) AND ('removemember' == $_POST['action']) ) {
		$model->removeMember($_POST['user']);
		$session->msg("Removed member from " . $model->name . ".", 'ok');
	}

	//----------------------------------------------------------------------------------------------
	//	accept HTTP GET to remove members (user UID is given)
	//----------------------------------------------------------------------------------------------
	// eg /groups/editmembers/removemember_8237146489/Some-group

	if ( (true == array_key_exists('removemember', $req->args))
		AND (true == $db->objectExists('users_user', $req->args['removemember'])) ) {

		$model->removeMember($req->args['removemember']);
		$session->msg("Removed member from " . $model->name . ".", 'ok');
	}
	
	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/groups/actions/editmembers.if.page.php');
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['raUID'] = $model->alias;
	$page->render();

?>
