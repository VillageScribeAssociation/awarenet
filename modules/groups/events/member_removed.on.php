<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');

//-------------------------------------------------------------------------------------------------
//|	fired when a user is removed from a group
//-------------------------------------------------------------------------------------------------
//arg: module - module to which this event applies [string]
//arg: groupUID - UID of a Groups_Group object [string]
//arg: userUID - UID of a Users_User object [string]
//arg: position - member's title within the group, eg 'president', 'lineback', etc [string]
//arg: admin - determines whether this use can administer this group (yes|no) [string]

function groups__cb_member_removed($args) {
		global $kapenta;
		global $kapenta;
		global $theme;
		global $user;
		global $kapenta;
		global $notifications;


	//---------------------------------------------------------------------------------------------
	//	check arguments
	//---------------------------------------------------------------------------------------------
	if (false == array_key_exists('module', $args)) { return false; }
	if ('groups' != $args['module']) { return false; }

	if (false == array_key_exists('groupUID', $args)) { return false; }
	if (false == array_key_exists('userUID', $args)) { return false; }
	if (false == array_key_exists('position', $args)) { return false; }
	if (false == array_key_exists('admin', $args)) { return false; }

	$model = new Groups_Group($args['groupUID']);
	$userUID = $args['userUID'];

	//---------------------------------------------------------------------------------------------
	//	pull triggers
	//---------------------------------------------------------------------------------------------
	// $kapenta->page->doTrigger('groups', 'members-' . $model->UID);
	// $kapenta->page->doTrigger('groups', 'members-any');

	//---------------------------------------------------------------------------------------------
	//	create notice
	//---------------------------------------------------------------------------------------------
	$ext = $model->extArray();	
	$userName = $theme->expandBlocks('[[:users::name::userUID=' . $userUID . ':]]', '');
	$title = $userName . ' is no longer a member of ' . $ext['name'];
	$content = 'Former position within this group: ' . $args['position'];
	$url = $ext['viewUrl'];

	$nUID = $notifications->create(
		'groups', 'groups_group', $model->UID, 'member_added', 
		$title, $content, $url
	);

	//---------------------------------------------------------------------------------------------
	//	add user, their friends, admins and all extant members of the group
	//---------------------------------------------------------------------------------------------
	$notifications->addUser($nUID, $userUID);
	$notifications->addAdmins($nUID);

	$members = $model->getMembers();
	foreach($members as $membership) { $notifications->addUser($nUID, $membership['userUID']); }

	//---------------------------------------------------------------------------------------------
	//	done
	//---------------------------------------------------------------------------------------------
	return true;
}

//-------------------------------------------------------------------------------------------------
?>
