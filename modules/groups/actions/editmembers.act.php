<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');

//--------------------------------------------------------------------------------------------------
//*	iframe to display and add group members, reference should be a group's recordalias
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------

	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }
	$UID = $aliases->findRedirect('groups_group');

	$model = new Groups_Group($UID);
	if (false == $model->loaded) { $kapenta->page->do404('no such group'); }

	//----------------------------------------------------------------------------------------------
	//	determine if current user is authorised to administer this group
	//----------------------------------------------------------------------------------------------
	$admin = $model->hasEditAuth($kapenta->user->UID);		//TODO: use a permission for this
	$members = $model->getMembers();
	foreach($members as $member) { 
		if (($member['userUID'] == $kapenta->user->UID) AND ('yes' == $member['admin'])) { $admin = true; } 
	}

	if ($admin == false) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	accept HTTP POSTs to add new members
	//----------------------------------------------------------------------------------------------
	if ( (array_key_exists('action', $_POST) == true)
		AND ($_POST['action'] == 'addMember') ) {

		$model->addMember($_POST['user'], $_POST['position'], $_POST['admin']);
		$kapenta->session->msg("Added new member to " . $model->name . ".", 'ok');
	}

	//----------------------------------------------------------------------------------------------
	//	accept HTTP POSTs to remove members
	//----------------------------------------------------------------------------------------------
	if ( (true == array_key_exists('action', $_POST)) AND ('removemember' == $_POST['action']) ) {
		$model->removeMember($_POST['user']);
		$kapenta->session->msg("Removed member from " . $model->name . ".", 'ok');
	}

	//----------------------------------------------------------------------------------------------
	//	accept HTTP GET to remove members (user UID is given)
	//----------------------------------------------------------------------------------------------
	// eg /groups/editmembers/removemember_8237146489/Some-group

	if ( (true == array_key_exists('removemember', $kapenta->request->args))
		AND (true == $kapenta->db->objectExists('users_user', $kapenta->request->args['removemember'])) ) {

		$model->removeMember($kapenta->request->args['removemember']);
		$kapenta->session->msg("Removed member from " . $model->name . ".", 'ok');
	}
	
	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/groups/actions/editmembers.if.page.php');
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['raUID'] = $model->alias;
	$kapenta->page->render();

?>
