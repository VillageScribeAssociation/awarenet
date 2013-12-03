<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');

//--------------------------------------------------------------------------------------------------
//*	add a member to a group, return HTML snippet to browser JS client
//--------------------------------------------------------------------------------------------------
//+	a groupUID and userUID should be posted along with 'addMember as the form action
//postarg: action - set to 'addMember' [string]
//postarg: userUID - UID fo a Users_User object to add to the group [string]
//postarg: groupUID - UID of a Groups_Group object [string]
//postarg: position - user's role within the group, just a label [string]
//postarg: admin - can this user administer this group (yes|no) [string]


	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) 
		{ echo "<span class='ajaxerror'>Action not given.</span>"; die(); }

	if ('addMember' != $_POST['action']) 
		{ echo "<span class='ajaxerror'>Action not recognized.</span>"; die(); }

	if (false == array_key_exists('userUID', $_POST)) 
		{ echo "<span class='ajaxerror'>User UID not given.</span>"; die(); }

	if (false == array_key_exists('groupUID', $_POST)) 
		{ echo "<span class='ajaxerror'>Group UID not given.</span>"; die(); }

	if (false == array_key_exists('position', $_POST)) 
		{ echo "<span class='ajaxerror'>Position not given.</span>"; die(); }

	if (false == array_key_exists('admin', $_POST)) 
		{ echo "<span class='ajaxerror'>Admin status not given.</span>"; die(); }

	$userUID = $_POST['userUID'];
	$groupUID = $_POST['groupUID'];
	$position = $utils->cleanTitle($_POST['position']);
	$isAdmin = $utils->cleanYesNo($_POST['admin']);

	if (false == $db->objectExists('users_user', $userUID)) 
		{ echo "<span class='ajaxerror'>User not recognized.</span>"; die(); }

	$model = new Groups_Group($_POST['groupUID']);
	if (false == $model->loaded) { echo "<span class='ajaxerror'>Unknown group.</span>"; die(); }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	add the member, raise event and we're done
	//----------------------------------------------------------------------------------------------
	$model->addMember($userUID, $position, $isAdmin);		//	this also updates school index

	$model->members->deleteDuplicates();					//	remove any duplicate memberships

	$args = array(
		'module' => 'groups',
		'groupUID' => $model->UID,
		'userUID' => $userUID,
		'position' => $position,
		'admin' => $isAdmin
	);

	$kapenta->raiseEvent('groups', 'member_added', $args);
	echo "<span class='ajaxmsg'>Added to group.</span>";

?>
